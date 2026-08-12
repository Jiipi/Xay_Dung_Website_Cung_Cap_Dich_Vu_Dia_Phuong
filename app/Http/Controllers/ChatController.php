<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Danh sách cuộc hội thoại.
     */
    public function index()
    {
        $userId = Auth::id();

        $conversations = Conversation::with(['khachHang', 'nhaCungCap', 'latestMessage'])
            ->where('khach_hang_id', $userId)
            ->orWhere('nha_cung_cap_id', $userId)
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($conv) use ($userId) {
                $otherUser = $conv->khach_hang_id === $userId
                    ? $conv->nhaCungCap
                    : $conv->khachHang;

                return [
                    'id' => $conv->id,
                    'other_user' => [
                        'id' => $otherUser?->id,
                        'name' => $otherUser?->ho_ten ?? 'Người dùng',
                        'avatar' => $otherUser?->anh_dai_dien,
                    ],
                    'last_message' => $conv->latestMessage?->content,
                    'last_message_time' => $conv->latestMessage?->created_at?->diffForHumans(),
                    'unread_count' => $conv->unreadCountFor($userId),
                ];
            });

        return Inertia::render('chat/Index', [
            'conversations' => $conversations,
            'authUserId' => $userId,
        ]);
    }

    /**
     * Lấy tin nhắn cho 1 cuộc hội thoại (JSON API).
     */
    public function show(Conversation $conversation)
    {
        $userId = Auth::id();
        if ($conversation->khach_hang_id !== $userId && $conversation->nha_cung_cap_id !== $userId) {
            abort(403);
        }

        // Mark unread messages as read
        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $conversation->messages()
            ->with('sender:id,ho_ten,anh_dai_dien')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'conversation_id' => $m->conversation_id,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender?->ho_ten ?? 'Người dùng',
                'sender_avatar' => $m->sender?->anh_dai_dien,
                'content' => $m->content,
                'is_read' => $m->is_read,
                'created_at' => $m->created_at?->toISOString(),
            ]);

        $otherUser = $conversation->khach_hang_id === $userId
            ? $conversation->nhaCungCap
            : $conversation->khachHang;

        return response()->json([
            'messages' => $messages,
            'other_user' => [
                'id' => $otherUser?->id,
                'name' => $otherUser?->ho_ten ?? 'Người dùng',
                'avatar' => $otherUser?->anh_dai_dien,
            ],
        ]);
    }

    /**
     * Gửi tin nhắn mới.
     */
    public function store(Request $request, Conversation $conversation)
    {
        $userId = Auth::id();
        if ($conversation->khach_hang_id !== $userId && $conversation->nha_cung_cap_id !== $userId) {
            abort(403);
        }

        $request->validate(['content' => 'required|string|max:5000']);

        $message = $conversation->messages()->create([
            'sender_id' => $userId,
            'content' => $request->content,
        ]);

        $conversation->touch(); // bump updated_at for sorting

        $message->load('sender:id,ho_ten,anh_dai_dien');

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender?->ho_ten,
            'sender_avatar' => $message->sender?->anh_dai_dien,
            'content' => $message->content,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at?->toISOString(),
        ]);
    }

    /**
     * Tạo hoặc lấy conversation giữa khách và nhà cung cấp.
     */
    public function createOrGet(Request $request)
    {
        $request->validate([
            'nha_cung_cap_id' => 'required|integer|exists:nguoi_dung,id',
        ]);

        $userId = Auth::id();
        $providerId = $request->nha_cung_cap_id;

        if ($providerId === $userId) {
            return response()->json(['message' => 'Không thể tự tạo cuộc trò chuyện với chính mình.'], 422);
        }

        $provider = User::with('vaiTroNguoiDung')->findOrFail($providerId);

        if ($provider->vaiTroNguoiDung?->ten_vai_tro !== 'Nhà cung cấp') {
            return response()->json(['message' => 'Người nhận không phải là nhà cung cấp hợp lệ.'], 422);
        }

        $conversation = Conversation::where(function ($q) use ($userId, $providerId) {
            $q->where('khach_hang_id', $userId)->where('nha_cung_cap_id', $providerId);
        })->orWhere(function ($q) use ($userId, $providerId) {
            $q->where('khach_hang_id', $providerId)->where('nha_cung_cap_id', $userId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'khach_hang_id' => $userId,
                'nha_cung_cap_id' => $providerId,
            ]);
        }

        return response()->json(['conversation_id' => $conversation->id]);
    }
}

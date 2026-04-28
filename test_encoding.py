import os
import sys

def test_decode():
    filepath = 'resources/js/pages/search/Index.vue'
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Let's find a line with mojibake
    lines = content.split('\n')
    for i, line in enumerate(lines):
        if "quáº£" in line or "TĂ¬m" in line or "TÃ¬M" in line or "Káº¿t" in line:
            print(f"Original: {line.strip()}")
            try:
                fixed1 = line.encode('windows-1252').decode('utf-8')
                print(f"Windows-1252 -> UTF-8: {fixed1.strip()}")
            except Exception as e:
                print(f"Windows-1252 failed: {e}")
            try:
                fixed2 = line.encode('latin1').decode('utf-8')
                print(f"Latin1 -> UTF-8: {fixed2.strip()}")
            except Exception as e:
                print(f"Latin1 failed: {e}")
            try:
                fixed3 = line.encode('utf-8').decode('windows-1252')
                print(f"UTF-8 -> Windows-1252: {fixed3.strip()}")
            except Exception as e:
                pass
            print("-" * 40)

if __name__ == '__main__':
    test_decode()

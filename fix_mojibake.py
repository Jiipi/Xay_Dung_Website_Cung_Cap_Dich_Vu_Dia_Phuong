import os

def fix_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if "Káº¿t" in content or "TĂ¬m" in content or "dá»‹ch" in content or "áº" in content:
        print(f"Fixing {filepath}...")
        try:
            # The files were read as CP1258 and saved as UTF-8. 
            # So the content is currently valid UTF-8 strings representing mojibake.
            # To reverse: encode back to CP1258 to get original UTF-8 bytes, then decode as UTF-8.
            raw_bytes = content.encode('cp1258')
            restored_text = raw_bytes.decode('utf-8')
            
            with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
                f.write(restored_text)
            print(f"  -> SUCCESS: {filepath}")
        except Exception as e:
            print(f"  -> FAILED: {filepath} ({e})")
            # If cp1258 fails, try windows-1252 as a fallback
            try:
                raw_bytes = content.encode('windows-1252')
                restored_text = raw_bytes.decode('utf-8')
                with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
                    f.write(restored_text)
                print(f"  -> SUCCESS (using windows-1252): {filepath}")
            except Exception as e2:
                print(f"  -> COMPLETELY FAILED: {filepath} ({e2})")

if __name__ == '__main__':
    for root, dirs, files in os.walk('resources'):
        for file in files:
            if file.endswith('.vue') or file.endswith('.ts') or file.endswith('.php'):
                fix_file(os.path.join(root, file))

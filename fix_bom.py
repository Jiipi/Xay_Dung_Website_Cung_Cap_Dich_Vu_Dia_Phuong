import os

def force_utf8_nobom(filepath):
    try:
        with open(filepath, 'rb') as f:
            raw = f.read()
        
        # remove BOM if exists
        if raw.startswith(b'\xef\xbb\xbf'):
            raw = raw[3:]
            
        text = raw.decode('utf-8')
        
        with open(filepath, 'w', encoding='utf-8', newline='\n') as f:
            f.write(text)
        print(f"Fixed {filepath}")
    except Exception as e:
        print(f"Failed {filepath}: {e}")

if __name__ == '__main__':
    for root, dirs, files in os.walk('resources'):
        for file in files:
            if file.endswith('.vue') or file.endswith('.blade.php') or file.endswith('.ts') or file.endswith('.js'):
                force_utf8_nobom(os.path.join(root, file))

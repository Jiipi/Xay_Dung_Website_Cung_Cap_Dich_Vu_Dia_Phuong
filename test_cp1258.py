def test():
    text = "TĂ¬m kiáº¿m dá»‹ch vá»¥"
    print("Original:", text)
    try:
        fixed = text.encode('cp1258').decode('utf-8')
        print("CP1258 -> UTF-8:", fixed)
    except Exception as e:
        print("CP1258 failed:", e)

if __name__ == '__main__':
    test()

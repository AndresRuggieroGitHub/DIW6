# fix_enc.py
import os

folder = r"C:\Users\andre\Desktop\lexi"
files = ["index.html","perfil.html","carrito.html","ejercicios.html","contacto.html","producto.html","progreso.html","info.html","biblioteca.html"]

for fname in files:
    path = os.path.join(folder, fname)
    with open(path, 'rb') as f:
        raw = f.read()
    
    # Try to decode as UTF-8 and detect corruption level
    # Corruption: UTF-8 bytes decoded as latin-1 then re-encoded as UTF-8
    # Fix: read as UTF-8 string, encode as latin-1, decode as UTF-8
    
    content = raw.decode('utf-8', errors='replace')
    
    # Count how many passes needed
    passes = 0
    test = content
    while 'Ã' in test and passes < 4:
        try:
            test = test.encode('latin-1').decode('utf-8')
            passes += 1
        except:
            break
    
    if passes > 0:
        fixed = content
        for _ in range(passes):
            fixed = fixed.encode('latin-1').decode('utf-8')
        with open(path, 'w', encoding='utf-8') as f:
            f.write(fixed)
        print(f"Fixed {fname} with {passes} pass(es)")
    else:
        print(f"OK (no fix needed): {fname}")

print("DONE")
# Verify
with open(os.path.join(folder, "index.html"), encoding='utf-8') as f:
    content = f.read()
idx = content.find("30 idiomas")
if idx >= 0:
    print(f"Verification: '{content[idx-6:idx+10]}'")

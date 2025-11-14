from convert_pdf import convert_from_path
import os

# ตำแหน่ง Poppler
poppler_path = r"C:\poppler\Library\bin"

pdf_dir = r"C:\Users\ASUS\Downloads"
pdf_files = [f"1 ({i}).pdf" for i in range(8, 16)]

output_dir = os.path.join(pdf_dir, "jpg_output")
os.makedirs(output_dir, exist_ok=True)

for pdf in pdf_files:
    pdf_path = os.path.join(pdf_dir, pdf)
    print(f"กำลังแปลง: {pdf_path}")

    images = convert_from_path(pdf_path, dpi=150, poppler_path=poppler_path)
    
    for i, img in enumerate(images):
        out_name = os.path.join(output_dir, f"{pdf[:-4]}_page{i+1}.jpg")
        img.save(out_name, "JPEG", quality=85, optimize=True)
        print(f"บันทึก: {out_name}")

print("✅ เสร็จแล้ว! ไฟล์ JPG อยู่ในโฟลเดอร์ jpg_output")

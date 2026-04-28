from PIL import Image

def process_favicon():
    try:
        # Open the original large image
        img = Image.open('app/images/favicon.png')

        # Convert to RGBA to ensure alpha channel exists
        if img.mode != 'RGBA':
            img = img.convert('RGBA')

        # Get bounding box of non-transparent pixels
        bbox = img.getbbox()

        if bbox:
            # Crop the image to the bounding box
            img_cropped = img.crop(bbox)
            
            # Make it square to fit perfectly in a favicon
            width, height = img_cropped.size
            max_dim = max(width, height)
            
            # Create a new square transparent image
            square_img = Image.new('RGBA', (max_dim, max_dim), (0, 0, 0, 0))
            
            # Paste the cropped image into the center
            offset_x = (max_dim - width) // 2
            offset_y = (max_dim - height) // 2
            square_img.paste(img_cropped, (offset_x, offset_y))
            
            # Save the cropped and squared image as the new public/favicon.png
            square_img.save('public/favicon.png')
            
            # Save as ICO with multiple sizes for perfect browser scaling
            square_img.save('public/favicon.ico', format='ICO', sizes=[(16, 16), (32, 32), (48, 48), (64, 64)])
            
            print(f"Success! Cropped from {img.size} to {square_img.size} and saved.")
        else:
            print("Image is entirely transparent.")
            
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    process_favicon()

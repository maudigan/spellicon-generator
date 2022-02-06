This will convert the native tga spell icon files from everquest into PNG, GIF or JPEG images

You can output them as indiviual 1x1 files, or 6x6 or any custom sheet size.
a CSS file describing each icons location will be included

IMPORTANT: This is not meant to be a publicly accessible utility on your site. It can only run once at a time.
E.G. If you start it on computer 1 and its at 50%, then open it on computer 2, it'll already be at 50% there too. Everyone can see the progress, and everyone can see every file generated. It is NOT Private and doesnt scale.

TO USE:


Put all your Spell*.tga files into the spelliconsheets directory, then run the index.php file.

Fill in the icons rows/cols, that will be how many icons are on each output image.
First File Index will be the first filenumber for your output files, each subsequent file will be +1 higher.
First Name is the template for the image filename. Dont put an extension. {FILENUM} gets replaced with the index automatically.
File Type... is the filetype
Background RGBA is the color of the background behind the icons. For transparent set the A to 127. for half transparent A would be 63.

Icon Number Start is the numbering of the style in the CSS, the first EQ icon defaults to 500
Output CSS Template is how the CSS lines will look, {ICON} replaces with the icon number, {FILE} replaces with the image file name, and the {X} and {Y} replace with the coordinates that the icon is at on the respective image.

Output CSS Filename is the name of the CSS file.

Once its filled out hit Generate.


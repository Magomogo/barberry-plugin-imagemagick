barberry-plugin-imagemagick
==========================

[![Build Status](https://travis-ci.org/Magomogo/barberry-plugin-imagemagick.png?branch=master)](https://travis-ci.org/Magomogo/barberry-plugin-imagemagick)

Barberry plugin for handling images

Parameters syntax:
-----------------

All parameters must be listed with no separators and the order of parameters cannot be changed.
Any parameter can be ommited.

    [width]x[height]bg[color]canvas[width]x[height]quality[percent]colorspace[name]

### [width]x[height]

Main info about height and width of corresponding image

### bg[color]

Background color of image in format 11AAFF (hex code format without # at the beginning)

### canvas[width]x[height]

Info about height and width of canvas
the real added params for image imagemagick will be:

    -size [width]x[height] xc:#[color] +swap -gravity center -composite

`[color]` by default is 000000 if no background color is set, otherwise uses bg color

### quality[percent]

Quality of jpeg image in percents. Range from 1 to 100

### colorspace[name]

The [colorspace](http://www.imagemagick.org/script/command-line-options.php#colorspace "imagemagick docs") of image.
Allowed colorspaces:

    Gray|CMYK|sRGB|Transparent|RGB

barberry-plugin-imagemagick
==========================

[![Build Status](https://travis-ci.org/Magomogo/barberry-plugin-imagemagick.png?branch=master)](https://travis-ci.org/Magomogo/barberry-plugin-imagemagick)

Barberry plugin for handling images

Parameters syntax:
-----------------

All parameters must be listed with no separators and the order of parameters cannot be changed.
Any parameter can be ommited.

    [width]x[height][noUpscale]bg[color]canvas[width]x[height]quality[percent]colorspace[name]strip

### [width]x[height][noUpscale]

Main info about height and width of corresponding image. In case `noUpscale` provided image will only shrink, but never grow.

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

### trim[color]x[percent]

[Remove](http://www.imagemagick.org/Usage/crop/#trim "imagemagick docs") any borders or edges of an image 
which did does not change in color or transparency.
In other words it removes the 'boring' bits surrounding an image.

`[color]` is color, which you want to trim

`[percent]` is percent of fuzz to be applied while trimming (more percent, possibly more image will be cut)

### strip

In some cases color profiles can take up significant part of your image, especially if image is small or resized. This option allows to cut color profiles out.

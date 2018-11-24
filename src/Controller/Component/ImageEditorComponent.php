<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\Folder;
use Cake\Network\Exception\NotFoundException;

class ImageEditorComponent extends Component
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setFileFormat('jpeg');
    }

    /**
     * Set data format
     *
     * @param $format
     */
    public function setFileFormat($format)
    {
        $this->fileFormat = $format;
    }

    /**
     * Instantiate the im object populated with the given image
     *
     * @param string $path
     */
    public function createFromImage(string $path)
    {
        $this->im = new \Imagick(realpath($path));
    }

    /**
     * Scale the image to the given width and automatically calculate the height
     * to preserve the aspect ratio
     *
     * @param int $width
     */
    public function resizeByWidth(int $width)
    {
        $this->im->scaleImage($width, 0);
    }

    /**
     * Crops the image
     *
     * @param int $width
     * @param int $height
     * @param int $startX
     * @param int $startY
     */
    public function crop(int $width, int $height, int $startX, int $startY)
    {
        $this->im->cropImage($width, $height, $startX, $startY);
    }

    /**
     * Save image into protected/finalimages and return the filename
     *
     * @return string filename
     */
    public function save()
    {
        $filename = uniqid() . rand(0, 9) . '.' . $this->fileFormat;
        $dir = new Folder(ROOT . DS . 'protected' . DS . 'finalimages', true);
        $this->path = $dir->path . DS . $filename;
        $this->im->setImageFormat($this->fileFormat);

        // if jpeg
        if (in_array(strtolower($this->fileFormat), ['jpeg', 'jpg'])) {
            // set best quality but least effective compression
            $this->im->setImageCompressionQuality(100);
            $this->im->setCompressionQuality(100);
            $this->im->setSamplingFactors(array('1x1', '1x1', '1x1'));
        }

        $this->im->writeImage($this->path);

        return $filename;
    }

    /**
     * add logo to $this->im
     *
     * @param \stdClass $bars
     * @param int $max_width
     * @param int $max_height
     *
     * @return boolean
     */
    public function addLogo(\stdClass $logo, int $max_width, int $max_height)
    {
        $rotation_angle = -5; // degrees

        // abort if there is no logo to add
        if (empty($logo->src)) {
            return true;
        }

        // generate the logo too big and scale it down later, to make sure we don't have
        // to heavy pixel rounding errors in the logo subline
        $scale_factor = $max_width < 1000 ? 4 : 1;
        $max_width *= $scale_factor;
        $max_height *= $scale_factor;
        $logo->width *= $scale_factor;
        $logo->height *= $scale_factor;
        $logo->fontsize *= $scale_factor;
        $logo->left *= $scale_factor;

        // canvas for logo
        $canvas = new \Imagick();

        // make the canvas a bit bigger as the max to be sure it will fit (angle)
        $width = $max_width * 1.5;
        $height = $max_height * 1.5;

        // create canvas
        $canvas->newImage($width, $height, 'transparent');

        // get top logo
        $top_logo = $this->_getTopLogoByURL((string)$logo->src, (float)$logo->width);

        // add it to the canvas
        $canvas->compositeImage($top_logo, \imagick::COMPOSITE_DEFAULT, 0, 0);

        // get subline
        $text = $this->_sanitizeText((string)$logo->subline);
        $subline = $this->_getSubline($text, (float)$logo->fontsize);

        // get subline position
        $top_logo_height = $top_logo->getimageheight();
        $subline_height = $subline->getimageheight();
        $subline_top = $top_logo_height + $subline_height / 12;
        $subline_left = $logo->left;

        // add it to the canvas
        $canvas->compositeImage($subline, \imagick::COMPOSITE_DEFAULT, $subline_left, $subline_top);

        // resize it to the actual size
        $canvas->resizeImage(
            round($width / $scale_factor),
            round($height / $scale_factor),
            \imagick::FILTER_LANCZOS,
            1);

        // cut transparent borders (do this now, and after rotation to make sure
        // the x and y pos are identical with the ones of the frontend)
        $canvas->trimImage(0);

        // rotate bar canvas
        $canvas->rotateImage('transparent', $rotation_angle);

        // cut transparent borders
        $canvas->trimImage(0);

        // get logo position
        $top = $logo->y_pos + $logo->margin;
        $left = $logo->x_pos + $logo->margin;

        // add bars to image
        $this->im->compositeImage($canvas, \imagick::COMPOSITE_DEFAULT, $left, $top);

        return true;
    }

    /**
     * Return a Imagick object with the top logo scaled to the given width
     *
     * @param string $url
     * @param float $width
     *
     * @return \Imagick
     * @throws NotFoundException
     * @throws \ImagickException
     */
    private function _getTopLogoByURL(string $url, float $width)
    {
        $file_name = pathinfo($url, PATHINFO_BASENAME);
        $logo_path = realpath(ROOT . DS . 'protected' . DS . 'logos' . DS . 'colored' . DS . $file_name);

        // check if top logo can be found
        if (!file_exists($logo_path)) {
            throw new NotFoundException(__('Logo not found.'));
        }

        // generate Imagick object of top logo
        $im = new \Imagick();
        $im->setBackgroundColor('transparent');
        $im->readimage($logo_path);

        // scale it to given size
        $im->scaleimage($width, 0);

        return $im;
    }

    /**
     * Replace ' by ’, stripe \n und make text uppercase
     *
     * @param string $text
     *
     * @return string
     */
    private function _sanitizeText(string $text)
    {
        $text = str_replace("'", '’', $text);
        $text = str_replace("\n", '', $text);
        $text = mb_strtoupper($text, 'UTF-8');
        $text = trim($text);
        $text = strip_tags($text);

        return $text;
    }

    /**
     * Return Imagick object with the logo subline
     *
     * @param string $text
     * @param float $fontsize
     *
     * @return \Imagick object of the bar
     */
    private function _getSubline(string $text, float $fontsize)
    {
        $color_scheme = 'magenta';
        $align = 'fit';
        $font_weight = 'Bold';
        $text = mb_strtoupper($text, 'UTF-8');

        return $this->_drawBar($text, $color_scheme, $fontsize, $align, $font_weight);
    }

    /**
     * Draws the bar on a Imagick object
     *
     * @param string $text
     * @param string $color_scheme
     * @param float $font_size
     * @param string $align
     * @param string $font_weight
     * @param float|null $shadow_width
     *
     * @return \Imagick object of the bar
     */
    private function _drawBar(
        string $text,
        string $color_scheme,
        float $font_size,
        string $align,
        string $font_weight = 'Fat',
        $shadow_width = null
    )
    {

        // if the font size is too small, generate it bigger and scale it down afterwards
        // to reduce the rounding errors
        $scale_factor = $font_size < 40 ? 4 : 1;
        $font_size *= $scale_factor;
        $shadow_width *= $scale_factor;

        // set colors
        switch ($color_scheme) {
            case 'green':
                $bar_color = new \ImagickPixel('rgb(132,180,20)');
                $text_color = new \ImagickPixel('#ffffff');
                break;

            case 'magenta':
                $bar_color = new \ImagickPixel('rgb(225,0,120)');
                $text_color = new \ImagickPixel('#ffffff');
                break;

            case 'white':
                $bar_color = new \ImagickPixel('#ffffff');
                $text_color = new \ImagickPixel('rgb(132,180,20)');
                break;

            default:
                return __('No valid color scheme given.');
                break;
        }

        // set font
        $path_to_font = ROOT . DS . 'protected' . DS . 'fonts' . DS . 'SanukOT-' . $font_weight . '.otf';

        // the fonts padding
        $padding = [
            'top' => 0,
            'right' => 'right' === $align ? 500 * $scale_factor : $font_size * 0.25,
            'bottom' => 0,
            'left' => 'left' === $align ? 500 * $scale_factor : $font_size * 0.25,
        ];

        // for some unknown reason we have to correct the placement of the text
        $corr_annotation_err = -$font_size * 0.05;

        // get text height
        // Pass Ü and J because they are the highest and the lowest chars
        $text_height = $this->_getTextGeometry('ÜJ', $font_size, $path_to_font)['height'];

        // get text width
        $text_width = $this->_getTextGeometry($text, $font_size, $path_to_font)['width'];

        // set image size
        $height = $text_height + $padding['top'] + $padding['bottom'];
        $width = $text_width + $padding['left'] + $padding['right'];

        // setup text draw object
        $draw = new \ImagickDraw();
        $draw->setFontSize($font_size);
        $draw->setFont(realpath($path_to_font));
        $draw->setFillColor($text_color);
        $draw->setGravity(\Imagick::GRAVITY_NORTHWEST);

        // make image
        $bar = new \Imagick();
        $bar->newImage($width, $height, $bar_color);

        // add shadow
        if ($shadow_width) {
            // create shadow
            $gradient = new \Imagick();
            $gradient->newPseudoImage(
                $height, // use height as width because we'll rotate the image later
                $shadow_width * 1.2, // 1.2 is just a factor to make it look nice
                "gradient:grey-white"
            );

            // rotate it
            $angle = 'left' === $align ? -90 : 90;
            $gradient->rotateimage('transparent', $angle);

            // add shadow to bar
            $x_pos = 'left' === $align ? $padding['left'] + $corr_annotation_err - $shadow_width
                : $text_width + $padding['left'];
            $bar->compositeimage(
                $gradient,
                \imagick::COMPOSITE_DEFAULT,
                $x_pos,
                0
            );
        }

        // add text
        $bar->annotateImage(
            $draw,
            $padding['left'] + $corr_annotation_err,
            $padding['top'] + $corr_annotation_err,
            0,
            $text
        );

        // resize bar to actual size
        $bar->resizeImage(
            round($width / $scale_factor),
            round($height / $scale_factor),
            \imagick::FILTER_LANCZOS,
            1);

        // return it
        return $bar;
    }

    /**
     * Get array with the texts width and height
     *
     * @param string $text
     * @param float $font_size
     * @param string $path_to_font
     *
     * @return array with the keys 'width' and 'height'. all in pixel
     */
    private function _getTextGeometry(string $text, float $font_size, string $path_to_font)
    {

        // draw the text
        $draw = new \ImagickDraw();
        $draw->setFontSize($font_size);
        $draw->setFont(realpath($path_to_font));
        $draw->setFillColor('black');
        $draw->setGravity(\Imagick::GRAVITY_NORTHWEST);
        $draw->annotation(0, 0, $text);

        // let's be sure we make the canvas big enough
        $canvas_width = $font_size * strlen($text);
        $canvas_height = $font_size * 2;

        // generate a transparent canvas
        $canvas = new \Imagick();
        $canvas->newImage($canvas_width, $canvas_height, 'transparent');

        // place the $text on it
        $canvas->drawImage($draw);

        // cut transparent borders
        $canvas->trimImage(0);

        // return the geometry
        return $canvas->getImageGeometry();
    }

    /**
     * add bars to $this->im
     *
     * @param \stdClass $bars
     * @param int $max_width
     * @param int $max_height
     * @param bool $has_border
     *
     * @return boolean
     */
    public function addBars(\stdClass $bars, int $max_width, int $max_height, bool $has_border)
    {
        $rotation_angle = -5; // degrees
        $y_pos_bars = (float)$bars->y_pos;
        $x_pos_bars = (float)$bars->x_pos;

        // tmp var
        $y_pos_next_bar = 0;

        // canvas for bars
        $canvas = new \Imagick();

        // make the canvas a bit bigger than the max to be sure it will fit (angle)
        $width = $max_width * 5;
        $height = $max_height * 5;

        // create canvas
        $canvas->newImage($width, $height, 'transparent');

        // keep headline font size to calculate shadows on subline bars
        $headlineFontSize = 0;

        // loop over bars
        foreach ($bars->data as $bar) {
            // get properties
            $color_scheme = $this->_parseColorScheme((string)$bar->type);
            $text = $this->_sanitizeText((string)$bar->text);
            $font_size = (float)$bar->fontsize;
            $align = $this->_parseAlign((string)$bar->type);
            $y_pos_bar = $y_pos_next_bar;
            $type = $this->_parseType((string)$bar->type);

            // use this for drawing shadows on white sublines
            if ('headline' === $type) {
                $headlineFontSize = $font_size;
            }

            // draw shadow if the image has a border and the bar is white
            if ($has_border && 'white' === $color_scheme) {
                $shadow_width = $headlineFontSize;
            } else {
                $shadow_width = false;
            }

            // draw the bar
            $b = $this->_drawBar($text, $color_scheme, $font_size, $align, 'Fat', $shadow_width);

            // compute position on the x axis
            $x_pos_bar = 'left' === $align ? 0 : $width - $b->getimagewidth();

            // add bar to bars canvas
            $canvas->compositeImage($b, \imagick::COMPOSITE_DEFAULT, $x_pos_bar, $y_pos_bar);

            // determine position of the next bar
            $y_pos_next_bar += $this->_getBarOuterHeight($b);
        }

        // cut transparent borders
        $canvas->trimImage(0);

        // rotate bar canvas
        $canvas->rotateImage('transparent', $rotation_angle);

        // add bars to image
        $this->im->compositeImage($canvas, \imagick::COMPOSITE_DEFAULT, $x_pos_bars, $y_pos_bars);

        return true;
    }

    /**
     * Returns the first scheme found in the given string. Returns false if no
     * scheme was found
     *
     * @param string $html_classes
     *
     * @return mixed
     */
    private function _parseColorScheme(string $html_classes)
    {
        $schemes = ['green', 'magenta', 'white'];

        foreach ($schemes as $scheme) {
            if (strpos($html_classes, $scheme) !== false) {
                return $scheme;
            }
        }

        return false;
    }

    /**
     * Returns the first align found in the given string. Returns false if no
     * align was found
     *
     * @param string $html_classes
     *
     * @return mixed
     */
    private function _parseAlign(string $html_classes)
    {
        $types = ['left', 'right'];

        foreach ($types as $type) {
            if (strpos($html_classes, $type) !== false) {
                return $type;
            }
        }

        return false;
    }

    /**
     * Returns the first type found in the given string. Returns false if no
     * type was found
     *
     * @param string $html_classes
     *
     * @return mixed
     */
    private function _parseType(string $html_classes)
    {
        $types = ['headline', 'subline'];

        foreach ($types as $type) {
            if (strpos($html_classes, $type) !== false) {
                return $type;
            }
        }

        return false;
    }

    /**
     * Returns height plus margin of given bar. the minimal margin is 1px
     *
     * @param \Imagick $img
     *
     * @return float
     */
    private function _getBarOuterHeight(\Imagick $img): float
    {
        $height = $img->getimageheight();
        $margin = $height / 12 < 1 ? 1 : $height / 12; // min 1 px

        return $height + $margin;
    }

    /**
     * If 'auto' === $border->type then add the border to the image else do nothing
     *
     * @param \stdClass $border with the border width and border type property
     * @param int $width of the image
     * @param int $height of the image
     *
     * @return boolean
     */
    public function addBorder(\stdClass $border, int $width, int $height)
    {
        if ('auto' !== $border->type) {
            return true;
        }

        // draw border with round corners
        $this->_drawBorder($border, $width, $height, true);

        // draw border with square corners to fill the corners
        $this->_drawBorder($border, $width, $height, false);
    }

    /**
     * If 'auto' === $border->type then add the border to the image else do nothing
     *
     * This method must be run twice: one for generating the border with the round corners.
     * A second time for filling the outside of the round corners.
     *
     * @param \stdClass $border with the border width and border type property
     * @param int $width of the image
     * @param int $height of the image
     * @param bool $round use true for generating the round corners, false for filling them
     *
     * @return boolean
     */
    private function _drawBorder(\stdClass $border, int $width, int $height, bool $round)
    {
        $border_width = (int)$border->width;
        $border_color = 'white';

        // create draw object
        $draw = new \ImagickDraw();

        // set colors
        $draw->setStrokeColor($border_color);
        $draw->setFillColor('transparent');
        $draw->setStrokeOpacity(1);

        // set border width
        $draw->setStrokeWidth($border_width);

        if ($round) {
            // compute rectangle coordinates (border is counted half)
            $start_x = $border_width / 2;
            $start_y = $border_width / 2;
            $stop_x = $width - $start_x;
            $stop_y = $height - $start_y;

            // rectangle corner radius starting point
            $corner_x = $border_width * 1.5; // it would be 2 but because half of the border is not counted it gets 1.5
            $corner_y = $border_width * 1.5; // it would be 2 but because half of the border is not counted it gets 1.5
        } else {
            $start_x = 0;
            $start_y = 0;
            $stop_x = $width;
            $stop_y = $height;

            $corner_x = $border_width;
            $corner_y = $border_width;
        }

        // draw a rectangle with round corners
        $draw->roundRectangle($start_x, $start_y, $stop_x, $stop_y, $corner_x, $corner_y);

        // add it to the image
        $this->im->drawImage($draw);

        return true;
    }

    /**
     * Returns gradient Imagick object
     *
     * @param \stdClass $size
     *
     * @return \Imagick
     */
    public function createWithGradient(\stdClass $size)
    {
        $color1 = 'rgb(100,150,28)';
        $color2 = 'rgb(176,195,0)';

        $rotation_angle = 7.5; // degrees

        $height = (int)$size->height;
        $width = (int)$size->width;

        // calculate the oversize we need to crop it after rotating
        $angle_corr_x = sin(deg2rad($rotation_angle)) * $height * 2;
        $angle_corr_y = sin(deg2rad($rotation_angle)) * $width * 2;

        // set gradient width
        $gradient_width = $width + $angle_corr_x;
        $gradient_height = $height + $angle_corr_y;

        // generate half gradients
        $im1 = new \Imagick();
        $im2 = new \Imagick();
        $im1->newPseudoImage(
            round($gradient_width, 0),
            round($gradient_height / 2, 0),
            "gradient:$color1-$color2"
        );
        $im2->newPseudoImage(
            round($gradient_width, 0),
            round($gradient_height / 2, 0),
            "gradient:$color2-$color1"
        );

        // compose half gradients to single canvas
        $gradient = new \Imagick();
        $gradient->newImage(
            round($gradient_width, 0),
            round($gradient_height, 0),
            'transparent'
        );
        $gradient->compositeimage(
            $im1,
            \imagick::COMPOSITE_DEFAULT,
            0,
            0
        );
        $gradient->compositeimage(
            $im2,
            \imagick::COMPOSITE_DEFAULT,
            0,
            round($gradient_height / 2, 0)
        );

        // rotate canvas and fill with gaps black
        $gradient->rotateImage('black', $rotation_angle);

        // make final image
        $image = new \Imagick();
        $image->newImage($width, $height, 'transparent');
        $image->compositeimage(
            $gradient,
            \imagick::COMPOSITE_DEFAULT,
            -$angle_corr_x,
            -$angle_corr_y
        );

        return $image;
    }

    /**
     * Set final image dimensions
     *
     * @param \stdClass $dims
     */
    private function _setSize(\stdClass $dims)
    {
        $this->width = $dims->width;
        $this->height = $dims->height;
    }

}
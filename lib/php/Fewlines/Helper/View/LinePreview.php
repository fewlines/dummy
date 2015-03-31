<?php
namespace Fewlines\Helper\View;

class LinePreview extends \Fewlines\Helper\AbstractViewHelper
{
    public function init() {
    }

    /**
     * Get a part of a file
     * defined by the depth
     *
     * @param  string  $file
     * @param  integer $line
     * @param  integer $depth
     * @return array
     */
    public function linePreview($file, $line, $depth = 3) {
        $file = file($file);
        $maxLines = count($file);

        $start = $line - $depth;
        $start = $start >= 0 ? $start : 0;

        $end = $line + $depth + 1;
        $end = $end > $maxLines ? $maxLines : $end;

        $lines = array();

        for ($i = $start; $i < $end; $i++) {
            $lines[$i + 1] = htmlspecialchars($file[$i]);
        }

        return $lines;
    }
}

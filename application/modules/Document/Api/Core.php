<?php

class Document_Api_Core extends Core_Api_Abstract
{

    public function deleteDocument($document)
    {
        // delete file
        Engine_Api::_()->getItem('storage_file', $document->file_id)->remove();

        // delete activity feed and its comments/likes
        $item = Engine_Api::_()->getItem('document', $document->document_id);
        if ($item) {
            $item->delete();
        }
    }

    public function subPhrase($string, $length = 0) {
        if (strlen ( $string ) <= $length)
            return $string;
        $pos = $length;
        for($i = $length - 1; $i >= 0; $i --) {
            if ($string [$i] == " ") {
                $pos = $i + 1;
                break;
            }
        }
        return substr ( $string, 0, $pos ) . "...";
    }

}
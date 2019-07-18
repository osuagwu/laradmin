<?php
// Security to prevent direct access of php files
if(!defined('ABSPATH')){
    exit;
}

/**
 * A wrapper for media_handle_sideload() to fascilitate creating its parameters.
 *
 * @param string $image_filename A local image absolute filename.
 * @param integer $id @see media_handle_sideload()
 * @return @see media_handle_sideload().
 */
function local_media_image($image_filename, $id=0){ 

    require_once(ABSPATH . '/wp-admin/includes/file.php');
    require_once(ABSPATH . '/wp-admin/includes/media.php');
    require_once(ABSPATH . '/wp-admin/includes/image.php');

    $array = array( //array to mimic $_FILES
        'name' => basename($image_filename), //isolates and outputs the file name from its absolute path
        'type' => wp_check_filetype($image_filename), // get mime type of image file
        'tmp_name' => $image_filename, //this field passes the actual path to the image
        'error' => 0, //normally, this is used to store an error, should the upload fail. but since this isnt actually an instance of $_FILES we can default it to zero here
        'size' => filesize($image_filename) //returns image filesize in bytes
    );

    return media_handle_sideload($array, $id); //the actual image processing, that is, move to upload directory, generate thumbnails and image sizes and writing into the database happens here

}
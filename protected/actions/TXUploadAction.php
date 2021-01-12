<?php
Yii::import( "xupload.actions.XUploadAction" );
Yii::import( "xupload.models.XUploadForm" );

/**
 * UploadAction based on XUploadAction
 * =============
 */
class TXUploadAction extends XUploadAction {
   
    
    private $_pass = '2FigaVsemVam';
    
    /**
     * Initialize the propeties of this action, if they are not set.
     *
     * @since 0.1
     */
    //public function init( ) {
    //    $file = new File();
    //    if( !isset( $this->path ) ) {
    //        $this->path = $file->getUploadPath();//realpath( Yii::app( )->getBasePath( )."/../uploads" );
    //    }
    //    if(!isset($this->publicPath)){
    //        $this->publicPath = Yii::app()->createUrl('patients/files/get');
    //    }
    //    
    //    if( !is_dir( $this->path ) ) {
    //        mkdir( $this->path, 0777, true );
    //        chmod ( $this->path , 0777 );
    //        //throw new CHttpException(500, "{$this->path} does not exists.");
    //    } else if( !is_writable( $this->path ) ) {
    //        chmod( $this->path, 0777 );
    //        //throw new CHttpException(500, "{$this->path} is not writable.");
    //    }
    //
    //    if( $this->subfolderVar !== null ) {
    //        $this->_subfolder = Yii::app( )->request->getQuery( $this->subfolderVar, date( "mdY" ) );
    //    } else if( $this->subfolderVar !== false ) {
    //        $this->_subfolder = date( "mdY" );
    //    }
    //}
    //
    ///**
    // * The main action that handles the file upload request.
    // * @since 0.1
    // * @author Asgaroth
    // */
    //public function run( ) {
    //    header( 'Vary: Accept' );
    //    if( isset( $_SERVER['HTTP_ACCEPT'] ) && (strpos( $_SERVER['HTTP_ACCEPT'], 'application/json' ) !== false) ) {
    //        header( 'Content-type: application/json' );
    //    } else {
    //        header( 'Content-type: text/plain' );
    //    }
    //
    //    if( isset( $_GET["_method"] ) ) {
    //        if( $_GET["_method"] == "delete" ) {
    //            $_GET["file"] = Common::encode($_GET["file"],$this->_pass);
    //            $success = is_file( $_GET["file"] ) && $_GET["file"][0] !== '.' && unlink( $_GET["file"] );
    //            echo json_encode( $success );
    //        }
    //    } else {
    //        $this->init( );
    //        $model = new XUploadForm;
    //        $model->file = CUploadedFile::getInstance( $model, 'file' );
    //        if( $model->file !== null ) {
    //            $model->mime_type = $model->file->getType( );
    //            $model->size = $model->file->getSize( );
    //            $model->name = $model->file->getName( );
    //            if( $model->validate( ) ) {
    //                $path = ($this->_subfolder != "") ? "{$this->path}/{$this->_subfolder}/" : "{$this->path}/";
    //                //$publicPath = ($this->_subfolder != "") ? "{$this->publicPath}/{$this->_subfolder}/" : "{$this->publicPath}/";
    //                $subfolder = ($this->_subfolder != "") ? "/{$this->_subfolder}/" :'';
    //                if( !is_dir( $path ) ) {
    //                    mkdir( $path, 0777, true );
    //                    chmod ( $path , 0777 );
    //                }
    //                $model->file->saveAs( $path.$model->name );
    //                chmod( $path.$model->name, 0777 );
    //                echo json_encode( array( array(
    //                        "name" => $model->name,
    //                        "type" => $model->mime_type,
    //                        "size" => $model->size,
    //                        "url" => $this->publicPath.'&name='.$subfolder.$model->name,
    //                        "thumbnail_url" => $this->publicPath.'&name='.$subfolder.$model->name,
    //                        "delete_url" => $this->getController( )->createUrl( "upload", array(
    //                            "_method" => "delete",
    //                            "file" => Common::encode($path.$model->name,$this->_pass)
    //                        ) ),
    //                        "delete_type" => "POST"
    //                    ) ) );
    //            } else {
    //                echo json_encode( array( array( "error" => $model->getErrors( 'file' ), ) ) );
    //                Yii::log( "XUploadAction: ".CVarDumper::dumpAsString( $model->getErrors( ) ), CLogger::LEVEL_ERROR, "xupload.actions.XUploadAction" );
    //            }
    //        } else {
    //            throw new CHttpException( 500, "Could not upload file" );
    //        }
    //    }
    //}

}

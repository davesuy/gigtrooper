<?php

namespace Gigtrooper\Services;

class S3FileUploadHandlerService extends BaseS3UploadHandlerService
{
	protected $element;

	public function setElement($element)
	{
		$this->element = $element;
	}

	protected function handle_form_data($file, $index) {
		$file->handle          = @$_POST['fileuploadHandle'];
		$file->fileuploadId    = @$_POST['fileuploadId'];
		$file->fileuploadLabel = @$_POST['fileuploadLabel'];
	}

	protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
	                                      $index = null, $content_range = null) {
		$file = parent::handle_file_upload(
			$uploaded_file, $name, $size, $type, $error, $index, $content_range
		);

		$fileUploadService = \App::make('fileUploadService');

		$fileUploadService->handle_file_upload($file);

		return $file;
	}

	/**
	 * @param bool $print_response
	 */
	public function delete($print_response = true)
	{
		$response = parent::delete(false);

		$fileUploadService = \App::make('fileUploadService');

		$fileUploadService->delete($response, $this->options);

		return $this->generate_response($response, $print_response);
	}
}
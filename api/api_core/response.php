<?php

class Response
{
	public static function json($status = 200, $message = 'success', $data = null)
	{
		header('Content-Type: application/json');

		if (!API_IS_ACTIVE) {
			return json_encode(
				array(
					'status' => 400,
					'message' => 'API Offline',
					'api_version' => API_VERSION,
					'datetime_response' => date('Y-m-d H:i:s'),
					'data' => null
				),
				JSON_PRETTY_PRINT
			);
		}

		return json_encode(
			array(
				'status' => $status,
				'message' => $message,
				'api_version' => API_VERSION,
				'datetime_response' => date('Y-m-d H:i:s'),
				'data' => $data
			),
			JSON_PRETTY_PRINT
		);
	}

}
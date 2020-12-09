<?php

namespace ubitcorp;

/**
 * Class for working with EasyQuiz API
 */
class Client
{
    use HTTPClient;

    // Properties
    protected $api_url = "https://api.easyquiz.co/v1";
    protected $client_key, $client_secret, $client;
    public $token;
    protected $error;


    /**
     * Constructor method
     *
     * @return Client instance
     */
    public function __construct($client_key, $client_secret)
    {
        $this->client_key = $client_key;
        $this->client_secret = $client_secret;

        return $this->getToken();
    }


    /**
     * Helper to get EasyQuiz Auth Token
     *
     * @return Client: Client instance
     */
    private function getToken()
    {

        try {
            $result = $this->makeRequest($this->api_url.'/token','POST', [],[
                'Accept: application/json',
                'ClientKey: ' . $this->client_key,
                'ClientSecret: ' . $this->client_secret
            ]);
            $this->token = $result['body']['data'];
            return $this;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }


    /**
     * Request Headers for EasyQuiz
     *
     * @return Array: Header part for Auth requests
     */
    private function headerWithToken()
    {
        return [
            'Accept:application/json',
            'Content-Type:application/json',
            'Authorization:Bearer ' . $this->token
        ];
    }

    /**
     * Make parameters to html query
     *
     * @params Required:
     * @param Array $data : Request Parameters as key => value
     *
     *
     * @return String: URL-encoded string.
     */
    private function buildHttpQueryParams($data = array())
    {
        return http_build_query($data);
    }


    /**
     * Get Exam Exports on EasyQuiz
     *
     * @params Optional: Filter For Exam Exports
     * @param Boolean $data ["reference_id"]
     * @param Boolean $data ["code"]
     * @param Boolean $data ["status"]
     * @param Boolean $data ["version"]
     * @param Boolean $data ["name"]
     *
     * @return Array: Associative array containing response header and body as 'header' and 'body' keys.
     */
    public function getExports($data = array())
    {
        try {
            return $this->makeRequest($this->api_url . '/exports/?&' . $this->buildHttpQueryParams($data), 'GET', $data, $this->headerWithToken());
        } catch (\Exception $e) {
            return $e;
        }
    }


    /**
     * Create or get Examinee url for EasyQuiz Exam
     *
     * @params Optional
     * @param Boolean $data ["force"] : Force for a new url
     * @param Array $data ["personal_details"] : id_number fields for examinee
     *
     * @params Required
     * @param String $data ["refererence_id"] : Reference to catch spesific examinee
     * @param Array $data ["personal_details"] : name-surname fileds required
     *
     * @return Array: Associative array containing response header and body as 'header' and 'body' keys.
     */
    public function getExamUrl($data = array())
    {
        if (!array_key_exists('force', $data))
            $data['force'] = false;

        if (!array_key_exists('code', $data)) {
            $this->error = [
                "message" => 'There is no "code" filed in parameter'
            ];

            throw new \Exception($this->error['message'], 400);
        }

        try {
            return $this->makeRequest($this->api_url . "/exports/" . $data['code'] . "/url", 'POST', json_encode($data), $this->headerWithToken());
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * Get Examinees on EasyQuiz
     *
     * @params Optional: Filter For Exam Exports
     * @param String $data ["reference_id"]
     * @param String $data ["code"]
     *
     * $params Required
     * @param String $export_code
     *
     * @return Array: Associative array containing response header and body as 'header' and 'body' keys.
     */
    public function getExaminees($data = array(), $export_code)
    {
        if (!$export_code) {
            $this->error = [
                "message" => 'There is no "export_code" filed in parameter'
            ];

            throw new \Exception($this->error['message'], 400);
        }

        try {
            return $this->makeRequest($this->api_url . '/export/' . $export_code . '/examinees/?' . $this->buildHttpQueryParams($data), 'GET', $data, $this->headerWithToken());
        } catch (\Exception $e) {
            return $e;
        }
    }


    /**
     * Create or get Examinee url for EasyQuiz Exam
     *
     * @params Required
     * @param String $name : Name of Exam
     * @param String $refererence_id : Reference to your spesific identity
     * @param array $conf :
     * ["lastLetter"]  Last letter of multiple choice configuration
     * ["language"] : Language code for exam / tr,en
     * @param array $parts : Array of exam parts with your spesific ids / (Math, English) => {"name": "Math", "reference_id": "123"}, {"name": "English", "reference_id": "123"}
     *
     * @return Array: Associative array containing response header and body as 'header' and 'body' keys.
     */
    public function createExam($name, $refererence_id, $conf, $parts)
    {
        $data = [
            'name' => $name,
            'reference_id' => $refererence_id,
            'conf' => $conf,
            'parts' => $parts
        ];

        try {
            return $this->makeRequest($this->api_url . "/exams", 'POST', json_encode($data), $this->headerWithToken());
        } catch (\Exception $e) {
            return $e;
        }
    }

}
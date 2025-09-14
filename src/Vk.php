<?php
class vk
{
    private $token;
    private $path = "https://api.vk.com/method/users.get?";
    private $api_version = '5.131';
    /**
     * Constructor for vk class.
     *
     * @param string $token vk api token
     */
    function __construct($token)
    {
        $this->token = $token;
    }
    /**
     * Returns the name of a user by his vk id
     *
     * @param int $vk_id vk user id
     * @return string|false the name of the user if found, false otherwise
     */
    function getName($vk_id)
    {
        $params = http_build_query([
            'user_ids' => $vk_id,
            'fields' => 'first_name,last_name',
            'access_token' => $this->token,
            'v' => $this->api_version
        ]);

        $url =  $this->path . $params;

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        $user = $data['response'][0];
        if (!empty($data['response'][0]))
            echo $user['first_name'] . " " . $user['last_name'];
        else
            echo false;
    }
}

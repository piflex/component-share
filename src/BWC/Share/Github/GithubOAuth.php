<?php

namespace BWC\Share\Github;

use BWC\Share\Net\HttpClient\HttpClientInterface;
use BWC\Share\Net\HttpStatusCode;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GithubOAuth
{
    const STATE_SESSION_KEY = 'github_state';


    /** @var  string */
    protected $clientId;

    /** @var string  */
    protected $secret;

    /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface */
    protected $session;

    /** @var \BWC\Share\Net\HttpClient\HttpClientInterface  */
    protected $httpClient;



    /**
     * @param string $clientId
     * @param string $secret
     * @param SessionInterface $session
     * @param \BWC\Share\Net\HttpClient\HttpClientInterface $httpClient
     */
    public function __construct($clientId, $secret, SessionInterface $session, HttpClientInterface $httpClient)
    {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->session = $session;
        $this->httpClient = $httpClient;
    }


    /**
     * @param array $scopes
     * @param null|string $redirectUri
     * @return RedirectResponse
     */
    public function start(array $scopes = null, $redirectUri = null)
    {
        $state = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->session->set(self::STATE_SESSION_KEY, $state);

        $params = array(
            'client_id' => $this->clientId,
            'state' => $state
        );

        if ($redirectUri) {
            $params['redirect_uri'] = $redirectUri;
        }
        if ($scopes) {
            $params['scope'] = implode(',', $scopes);
        }

        return new RedirectResponse('https://github.com/login/oauth/authorize?'.http_build_query($params));
    }

    /**
     * @param string $state
     * @return bool
     */
    public function isStateValid($state)
    {
        if (!$state) {
            return false;
        }

        $sessionState = $this->session->get(self::STATE_SESSION_KEY);
        $this->session->remove(self::STATE_SESSION_KEY);
        if ($state != $sessionState) {
            return false;
        }

        return true;
    }

    /**
     * @param string $code
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @return GithubAccessResponse
     */
    public function getAccessTokenFromCode($code)
    {
        $json = $this->httpClient->post(
            'https://github.com/login/oauth/access_token',
            array(),
            array(
                'client_id' => $this->clientId,
                'client_secret' => $this->secret,
                'code' => $code
            ),
            null,
            array(
                'Accept: application/json'
            )
        );

        if ($this->httpClient->getStatusCode() != HttpStatusCode::OK) {
            throw new HttpException($this->httpClient->getStatusCode(), $this->httpClient->getErrorText() . ' : '. $json);
        }

        /** @var GithubAccessResponse $result */
        $result = GithubAccessResponse::deserialize($json);

        if (!$result) {
            new \LogicException('Unable to parse github response: '.$json);
        }
        if ($result->error) {
            throw new HttpException(HttpStatusCode::INTERNAL_SERVER_ERROR, $result->error.': '.$result->error_description);
        }

        return $result;
    }

} 
<?php
// Include Class Object
// Name: content
// Description: web page content functions

// Render template, create html page view...

class content extends base {
    protected $request = array();
    protected $content = array();
    protected $result = array();

    function __construct($parameter = array())
    {
        parent::__construct();

        $this->request = array();
        $this->result = array(
            'status'=>200,
            'header'=>array(),
            'content'=>array()
        );

        // Analyse uri structure and raw environment variables, store into $this->request
        if ($this->request_decoder($parameter) === false)
        {
            // Error Log, error during reading input uri and parameters
            $this->message->error = 'Fail: Error during request_decoder';
        }
//echo '<pre>';
//print_r('request_decoder: <br>');
//print_r($this);
if ($this->request['source_type'] == 'data')
{
    $api_access_log = PATH_ASSET.'log'.DIRECTORY_SEPARATOR.'api_access_log.txt';
    if (!file_exists(dirname($api_access_log))) mkdir(dirname($api_access_log), 0755, true);
    file_put_contents($api_access_log,'REQUEST: '.$this->request['remote_ip'].' ['.date('D, d M Y H:i:s').']'.$_SERVER['REQUEST_URI']."\n",FILE_APPEND);
    if ($this->request['method'] == 'select_business_by_uri' AND !empty($this->request['option']['uri']))
    {
        file_put_contents($api_access_log,'REQUEST QUERY: uri = '.$this->request['option']['uri']."\n",FILE_APPEND);
    }
}

        // Generate the necessary components for the content, store separate component parts into $content
        // Read data from database (if applicable), only generate raw data from db
        // If any further complicate process required, leave it to render
        if ($this->result['status'] == 200 AND $this->build_content() === false)
        {
            // Error Log, error during building data object
            $this->message->error = 'Fail: Error during build_content';
        }
//print_r('build_content: <br>');
//print_r($this);
//exit();
        // Processing file, database and etc (basically whatever time consuming, process it here)
        // As some rendering methods may only need the raw data without going through all the file copy, modify, generate processes
        if ($this->result['status'] == 200 AND $this->generate_rendering() === false)
        {
            // Error Log, error during rendering
            $this->message->error = 'Fail: Error during render';
        }
//print_r('generate_rendering: <br>');
//print_r(filesize($this->content['target_file']['path']));
//print_r($this);
if ($this->request['source_type'] == 'data')
{
    if (isset($this->content['account']))
    {
        file_put_contents($api_access_log,'REQUEST Account: '.$this->content['account']['name'].'['.$this->content['account']['id'].']'."\n",FILE_APPEND);
    }
    file_put_contents($api_access_log,'RESULT: '.$this->result['content']."\n\n",FILE_APPEND);
}
//exit();
    }

    private function request_decoder($value = '')
    {
        if (is_array($value))
        {
            if (!empty($value['value']))
            {
                extract($value);
            }
            else
            {
                $option = $value;
                $value = '';
            }
        }
        if (empty($value))
        {
            $value = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }
        if (!isset($option)) $option = array();

        if (!empty($_GET))
        {
            $option = array_merge($option,$_GET);
            unset($_GET);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' AND !empty($_POST))
        {
            // default post request with json format Input and Output
            $option = array_merge($option,$_POST);
            //if (!isset($option['data_type'])) $option['data_type'] = 'json';
            unset($_POST);
        }

        $request_uri = trim(preg_replace('/^[\/]?'.FOLDER_SITE_BASE.'[\/]/','',$value),'/');
        $request_path = explode('/',$request_uri);

        $type = ['css','font','image','js'];
        $request_path_part = array_shift($request_path);
        if (in_array($request_path_part,$type))
        {
            $this->request['source_type'] = 'static_file';
            $this->request['file_type'] = $request_path_part;
            $this->request['file_uri'] = URI_ASSET.$this->request['file_type'].'/';
        }
        else
        {
            $type = ['xml','json'];
            if (in_array($request_path_part,$type))
            {
                $this->request['source_type'] = 'data';
                $this->request['file_type'] = $request_path_part;
                $this->request['file_uri'] = URI_ASSET.$this->request['file_type'].'/';
            }
            else
            {
                $this->request['source_type'] = 'data';
                $this->request['file_type'] = 'html';
                $this->request['file_uri'] = URI_SITE_BASE;
            }
        }
        $this->request['file_path'] = PATH_ASSET.$this->request['file_type'].DIRECTORY_SEPARATOR;
        if (file_exists(PATH_PREFERENCE.$this->request['file_type'].FILE_EXTENSION_INCLUDE))
        {
            include_once(PATH_PREFERENCE.$this->request['file_type'].FILE_EXTENSION_INCLUDE);
        }

        // HTML Page uri structure decoder
        switch ($this->request['source_type'])
        {
            case 'static_file':
                if (empty($request_path))
                {
                    // Folder forbid direct access
                    $this->result['status'] = 403;
                    return false;
                }

                $file_name = array_pop($request_path);
                $file_part = explode('.',$file_name);
                $this->request['document'] = array_shift($file_part);
                if (!empty($file_part)) $this->request['file_type'] = array_pop($file_part);
                $this->request['extension'] = [];
                if ($this->request['file_type'] == 'image')
                {
                    $image_size = array_keys($this->preference->image['size']);
                    $image_quality = array_keys($this->preference->image['quality']);
                    foreach ($file_part as $file_extension_index=>$file_extension)
                    {
                        if (in_array($file_extension,$image_size))
                        {
                            $this->request['extension']['image_size'] = $file_extension;
                            unset($file_part[$file_extension_index]);
                        }
                        if (in_array($file_extension,$image_quality))
                        {
                            $this->request['extension']['quality'] = $file_extension;
                            unset($file_part[$file_extension_index]);
                        }
                    }
                    unset($image_size);
                    unset($image_quality);
                }
                foreach ($file_part as $file_extension_index=>$file_extension)
                {
                    if ($file_extension == 'min')
                    {
                        $this->request['extension']['minify'] = $file_extension;
                        unset($file_part[$file_extension_index]);
                    }
                }
                ksort($this->request['extension']);
                if (!empty($file_part))
                {
                    // Put the rest part that is not an extension back to document name, e.g. jquery-1.11.8.min.js
                    $this->request['document'] .= '.'.implode('.',$file_part);
                }
                unset($file_part);
                $decoded_file_name = $this->request['document'];
                if (!empty($this->request['extension'])) $decoded_file_name .= '.'.implode('.',$this->request['extension']);
                if (!empty($this->request['file_type'])) $decoded_file_name .= '.'.$this->request['file_type'];

                if ($file_name != $decoded_file_name)
                {
                    // Error Handling, decoded file name is not consistent to requested file name
                    $this->result['status'] = 404;
                    return false;
                }

                $this->request['sub_path'] = $request_path;

                if (!empty($this->request['sub_path']))
                {
                    $this->request['file_path'] .= implode(DIRECTORY_SEPARATOR,$this->request['sub_path']).DIRECTORY_SEPARATOR;
                    $this->request['file_uri'] .= implode('/',$this->request['sub_path']).'/';
                }
                $this->request['file_path'] .= $file_name;
                $this->request['file_uri'] .= $file_name;
                unset($file_name);
                break;
            case 'data':
            default:
                //$request_path_part = array_shift($request_path);
                $module = ['profile','listing','business','business-amp','members'];
                if (in_array($request_path_part,$module))
                {
                    $this->request['module'] = $request_path_part;
                    $request_path_part = array_shift($request_path);
                }
                else
                {
                    $this->request['module'] = '';
                }

                switch ($this->request['module'])
                {
                    case 'listing':
                        $method = ['search','find',''];
                        if (in_array($request_path_part,$method))
                        {
                            $this->request['method'] = $request_path_part;
                            $request_path_part = array_shift($request_path);
                        }
                        else
                        {
                            $this->request['method'] = end($method);
                        }

                        switch ($this->request['method'])
                        {
                            case 'search':
                                $this->request['option'] = array('keyword'=> $request_path_part);
                                if (count($request_path)>=2)
                                {
                                    $option = ['where','screen','sort'];
                                    $path_max = floor(count($request_path)/2);
                                    for ($i=0; $i<$path_max; $i++)
                                    {
                                        if (!in_array( $request_path[$i*2],$option))
                                        {
                                            $this->message->error = __FILE__.'(line '.__LINE__.'): Construction Fail, unknown option ['.$request_path[$i*2].'] for '.$this->request['file_type'];
                                            break 2;
                                        }
                                        $this->request['option'][$request_path[$i*2]] = $request_path[$i*2+1];
                                    }
                                }
                                break;
                            case 'find':
                                $this->request['option'] = array('category'=> $request_path_part);
                                $location = ['state','region','suburb'];
                                $option = ['keyword','where','screen','sort'];
                                foreach($request_path as $request_path_part_index=>$request_path_part)
                                {
                                    // If it is not option key
                                    if (in_array($request_path_part,$option))
                                    {
                                        $request_path = array_slice($request_path,$request_path_part_index);
                                        break;
                                    }
                                    $this->request['option'][$location[$request_path_part_index]] = $request_path_part;
                                }
                                if (count($request_path)>=2)
                                {
                                    $path_max = floor(count($request_path)/2);
                                    for ($i=0; $i<$path_max; $i++)
                                    {
                                        if (!in_array( $request_path[$i*2],$option))
                                        {
                                            $this->message->error = __FILE__.'(line '.__LINE__.'): Construction Fail, unknown option ['.$request_path[$i*2].'] for '.$this->request['file_type'];
                                            break 2;
                                        }
                                        $this->request['option'][$request_path[$i*2]] = $request_path[$i*2+1];
                                    }
                                }
                                break;
                            default:
                                //$this->request['document'] = $request_path_part;
                        }
                        break;
                    case 'members':
                        $method = ['account','listing','dashboard'];
                        if (in_array($request_path_part,$method))
                        {
                            $this->request['method'] = $request_path_part;
                            $this->request['file_path'] .= $this->request['module'].DIRECTORY_SEPARATOR.$this->request['method'].DIRECTORY_SEPARATOR.'index.html';
                            $this->request['file_uri'] .= $this->request['module'].'/'.$this->request['method'];
                        }
                        else
                        {
                            // Error Handling, trying to access members without module specified or unrecognized module
                            $this->result['status'] = 301;
                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['module'].'/'.end($method);
                        }
                        $this->request['remote_ip'] = get_remote_ip();
                        break;
                    default:
                        $this->request['document'] = $request_path_part;
                        $this->request['file_path'] .= $this->request['document'].DIRECTORY_SEPARATOR.'index.html';
                        $this->request['file_uri'] .= $this->request['document'];

                        $this->request['remote_ip'] = get_remote_ip();
                }

                break;
        }

        $this->request['remote_ip'] = get_remote_ip();

        if (isset($_COOKIE['session_id']))
        {
            $this->request['session_id'] = $_COOKIE['session_id'];
        }

        if (isset($_SERVER['HTTP_AUTH_KEY']))
        {
            $this->request['auth_key'] = $_SERVER['HTTP_AUTH_KEY'];
        }

        if (isset($_SERVER['HTTP_REFERER']))
        {
            $this->request['http_referer'] = $_SERVER['HTTP_REFERER'];
        }


        $option_preset = ['data_type','document','file_type','extension','module','template','render'];
        foreach($option as $key=>$item)
        {
            // Options from GET, POST overwrite ones decoded from uri
            if (in_array($key,$option_preset))
            {
                $this->request[$key] = $item;
                unset($option[$key]);
            }
        }
        // dump the rest custom/unrecognized input variables into $request['option']
        $this->request['option'] = $option;
    }

    private function build_content()
    {
        // If session is set, try to get account from session
        if (!empty($this->request['session_id']))
        {
            $entity_account_session_obj = new entity_account_session();
            $method_variable = ['status'=>'OK','message'=>'','account_session_id'=>$this->request['session_id'],'remote_ip'=>$this->request['remote_ip']];

            $session = $entity_account_session_obj->validate_account_session_id($method_variable);
            if ($session == false)
            {
                // Error Handling, session validation failed, session_id invalid
                $this->message->notice = 'Session exists, but session validation failed';
            }
            else
            {
                $entity_account_obj = new entity_account($session['account_id']);
                if (empty($entity_account_obj->row))
                {
                    // Error Handling, session validation failed, session_id is valid, but cannot read corresponding account
                    $this->message->notice = 'Session Validation Succeed, but cannot find related account account';
                }
                else
                {
                    $this->content['account'] = end($entity_account_obj->row);
                }
                unset($entity_account_obj);
            }
            unset($session);
        }

        // If auth_key is set, try to get api account from auth_key
        if (!empty($this->request['auth_key']))
        {
            $entity_api_key_obj = new entity_api_key();
            $method_variable = ['api_key'=>$this->request['auth_key'],'remote_ip'=>$this->request['remote_ip']];
            $auth_id = $entity_api_key_obj->validate_api_key($method_variable);
            if ($auth_id === false)
            {
                // Error Handling, api key authentication failed
                $this->message->notice = 'Building: Api Key Authentication Failed';
                $this->content['api_result'] = [
                    'status'=>$method_variable['status'],
                    'message'=>$method_variable['message']
                ];
            }
            else
            {
                $entity_api_obj = new entity_api($this->content['account']['id']);
                if (empty($entity_api_obj->row))
                {
                    // Error Handling, session validation failed, api_key is valid, but cannot read corresponding account
                    $this->message->error = 'Api Key Authentication Succeed, but cannot find related api account';
                    $this->content['api_result'] = [
                        'status'=>'REQUEST_DENIED',
                        'message'=>'Cannot get account info, it might be suspended or temporarily inaccessible'
                    ];
                }
                else
                {
                    $this->content['account'] = end($entity_api_obj->row);
                }
                unset($entity_api_obj);
            }
            unset($auth_id);
        }

        // Regularize Content output format
        if (!isset($this->request['option']['format'])) $this->content['format'] = $this->request['file_type'];
        else $this->content['format'] = $this->request['option']['format'];

        if($this->content['format'] == 'html_tag')
        {
            $this->content['html_tag'] = array();
            if (isset($this->request['option']['html_tag']))
            {
                $this->content['html_tag'] = array_merge($this->content['html_tag'],$this->request['option']['html_tag']);
            }
            if (!isset($this->content['html_tag']['attr'])) $this->content['html_tag']['attr'] = array();
            switch($this->request['file_type']) {
                case 'css':
                    if (!isset($this->content['html_tag']['name'])) $this->content['html_tag']['name'] = 'link';
                    $this->content['html_tag']['attr']['href'] = $this->request['file_uri'];
                    if (!isset($this->content['html_tag']['attr']['type'])) $this->content['html_tag']['attr']['type'] = 'text/css';
                    if (!isset($this->content['html_tag']['attr']['rel'])) $this->content['html_tag']['attr']['rel'] = 'stylesheet';
                    if (!isset($this->content['html_tag']['attr']['media'])) $this->content['html_tag']['attr']['media'] = 'all';
                    break;
                case 'image':
                    if (!isset($this->content['html_tag']['name'])) $this->content['html_tag']['name'] = 'img';
                    $this->content['html_tag']['attr']['src'] = $this->request['file_uri'];
                    if (!isset($this->content['html_tag']['attr']['alt'])) $this->content['html_tag']['attr']['alt'] = trim(ucwords($this->format->caption($this->request['document'])));
                    break;
                case 'js':
                    if (!isset($this->content['html_tag']['name'])) $this->content['html_tag']['name'] = 'script';
                    $this->content['html_tag']['attr']['src'] = $this->request['file_uri'];
                    if (!isset($this->content['html_tag']['attr']['type'])) $this->content['html_tag']['attr']['type'] = 'text/javascript';
                default:
                    // Error Handling, tag name not given
                    if (!isset($this->content['html_tag']['name'])) $this->content['html_tag']['name'] = 'div';
            }
        }

        switch($this->request['source_type'])
        {
            case 'static_file':
                $this->content['target_file'] = [
                    'path'=>$this->request['file_path'],
                    'uri'=>$this->request['file_uri']
                ];

                if (file_exists($this->content['target_file']['path']))
                {
                    $this->content['target_file']['last_modified'] = filemtime($this->content['target_file']['path']);
                    $this->content['target_file']['content_length'] = filesize($this->content['target_file']['path']);
                }
                else
                {
                    $this->content['target_file']['last_modified'] = 0;
                    $this->content['target_file']['content_length'] = 0;
                }

                $file_relative_path = $this->request['file_type'].DIRECTORY_SEPARATOR;
                if (!empty($this->request['sub_path'])) $file_relative_path .= implode(DIRECTORY_SEPARATOR,$this->request['sub_path']).DIRECTORY_SEPARATOR;
                $this->content['source_file'] = [
                    'path' => PATH_ASSET.$file_relative_path.$this->request['document'].'.src.'.$this->request['file_type'],
                    'source' => 'local_file'
                ];
                $source_file_relative_path =  $file_relative_path .  $this->request['document'].'.src.'.$this->request['file_type'];
                $file_relative_path .= $this->request['document'].'.'.$this->request['file_type'];


                if (!file_exists(dirname($this->content['source_file']['path']))) mkdir(dirname($this->content['source_file']['path']), 0755, true);

                if (isset($this->request['option']['source']))
                {
                    if ((strpos($this->request['option']['source'],URI_SITE_BASE) == FALSE)  AND (preg_match('/^http/',$this->request['option']['source']) == 1))
                    {
                        // If source_file is not relative uri and not start with current site uri base, it is an external (cross domain) source file
                        $this->content['source_file']['original_file'] = $this->request['option']['source'];
                        $this->content['source_file']['source'] = 'remote_file';
                    }
                    else
                    {
                        $this->content['source_file']['original_file'] = str_replace(URI_SITE_BASE,PATH_SITE_BASE,$this->request['option']['source']);
                    }

                    if ($this->content['source_file']['source'] == 'local_file')
                    {
                        $this->content['source_file']['last_modified'] = filemtime($this->content['source_file']['original_file']);
                        $this->content['source_file']['content_length'] = filesize($this->content['source_file']['original_file']);
                    }
                    else
                    {
                        // External source file
                        $file_header = @get_headers($this->content['source_file']['original_file'],true);
                        if (strpos( $file_header[0], '200 OK' ) === false) {
                            // Error Handling, fail to get external source file header
                            $this->message->error = 'Source File not accessible - '.$file_header[0];
                            return false;
                        }
                        if (isset($file_header['Last-Modified'])) {
                            $this->content['source_file']['last_modified'] = strtotime($file_header['Last-Modified']);
                        } else {
                            if (isset($file_header['Expires'])) {
                                $this->content['source_file']['last_modified'] = strtotime($file_header['Expires']);
                            } else {
                                if (isset($file_header['Date'])) $this->content['source_file']['last_modified'] = strtotime($file_header['Date']);
                                else $this->content['source_file']['last_modified'] = ('+1 day');
                            }
                        }
                        if (isset($file_header['Content-Length']))
                        {
                            $this->content['source_file']['content_length'] = $file_header['Content-Length'];
                            if ($this->content['source_file']['content_length'] > 10485760)
                            {
                                // Error Handling, source file too big
                                $this->message->error = 'Source File too big ( > 10MB )';
                                return false;
                            }
                        }
                        if (isset($file_header['Content-Type']))
                        {
                            $this->content['source_file']['content_type'] = $file_header['Content-Type'];
                        }
                    }
                }
                else
                {
                    if (file_exists(PATH_ASSET.$source_file_relative_path))
                    {
                        $this->content['source_file']['original_file'] = PATH_ASSET.$source_file_relative_path;
                        $this->content['source_file']['content_length'] = filesize($this->content['source_file']['original_file']);
                        $this->content['source_file']['last_modified'] = filemtime($this->content['source_file']['original_file']);
                    }
                    elseif (file_exists(PATH_ASSET.$file_relative_path))
                    {
                        $this->content['source_file']['original_file'] = PATH_ASSET.$file_relative_path;
                        $this->content['source_file']['content_length'] = filesize($this->content['source_file']['original_file']);
                        $this->content['source_file']['last_modified'] = filemtime($this->content['source_file']['original_file']);
                    }
                    elseif (file_exists(PATH_CONTENT.$file_relative_path))
                    {
                        $this->content['source_file']['original_file'] = PATH_CONTENT.$file_relative_path;
                        $this->content['source_file']['content_length'] = filesize($this->content['source_file']['original_file']);
                        $this->content['source_file']['last_modified'] = filemtime($this->content['source_file']['original_file']);
                    }
                    else
                    {
                        // If file source doesn't exist in content folder, try database
                        $document_name_part = explode('-',$this->request['document']);
                        $document_id = end($document_name_part);
                        if (empty($document_id) OR !is_numeric($document_id))
                        {
                            // Error Handling, fail to get source file from database, last part of file name is not a valid id
                            $this->message->error = 'Building: fail to get source file from database, file not in standard format';
                            $this->result['status'] = 404;
                            return false;
                        }
                        $entity_class = 'entity_'.$this->request['file_type'];
                        if (!class_exists($entity_class))
                        {
                            // Error Handling, last ditch failed, source file does not exist in database either
                            $this->message->error = 'Building: cannot find source file';
                            $this->result['status'] = 404;
                            return false;
                        }
                        $entity_obj = new $entity_class($document_id);
                        if (empty($entity_obj->row))
                        {
                            // Error Handling, fail to get source file from database, cannot find matched record
                            $this->message->error = 'Building: fail to get source file from database, invalid id';
                            $this->result['status'] = 404;
                            return false;
                        }
                        $record = array_shift($entity_obj->row);

                        if (empty($record['data']))
                        {
                            // Error Handling, image record found, but image data is not stored in database
                            $this->message->error = 'Building: fail to get source file from database, image data not stored';
                            $this->result['status'] = 404;
                            return false;
                        }

                        $this->content['source_file']['source'] = 'local_data';
                        $this->content['source_file']['original_file'] = $this->content['source_file']['path'];

                        if (!empty($record['update_time']))
                        {
                            $this->content['source_file']['last_modified'] = strtotime($record['update_time']);
                        }
                        else
                        {
                            $this->content['source_file']['last_modified'] = time();
                        }

                        if ($this->content['source_file']['last_modified'] > $this->content['target_file']['last_modified'])
                        {
                            file_put_contents($this->content['source_file']['path'],$record['data']);
                            touch($this->content['source_file']['path'], $this->content['source_file']['last_modified']);
                        }

                        if (!empty($record['mime'])) $this->content['source_file']['content_type'] = $record['mime'];
                    }
                }

                if ($this->content['source_file']['last_modified'] > $this->content['target_file']['last_modified'])
                {
                    if(file_exists($this->content['target_file']['path'])) unlink($this->content['target_file']['path']);
                }

                if ($this->content['source_file']['path'] == $this->content['source_file']['original_file'])
                {
                    unset($this->content['source_file']['original_file']);
                }
                else
                {
                    if ($this->content['source_file']['last_modified'] > $this->content['target_file']['last_modified'])
                    {
                        copy($this->content['source_file']['original_file'],$this->content['source_file']['path']);
                        touch($this->content['source_file']['path'], $this->content['source_file']['last_modified']);

                        if(!isset($this->content['source_file']['content_length'])) $this->content['source_file']['content_length'] = filesize($this->content['source_file']['path']);
                        if(!isset($this->content['source_file']['content_type'])) $this->content['source_file']['content_type'] = mime_content_type($this->content['source_file']['path']);
                    }
                    else
                    {
                        if(file_exists($this->content['source_file']['path'])) unlink($this->content['source_file']['path']);
                    }
                }

                if ($this->request['file_type'] == 'image')
                {
                    $source_image_size = getimagesize($this->content['source_file']['path']);
                    $this->content['source_file']['width'] = $source_image_size[0];
                    $this->content['source_file']['height'] = $source_image_size[1];

                    if (!isset($this->content['default_file'])) $this->content['default_file'] = [];
                    $this->content['default_file']['path'] = PATH_ASSET.$file_relative_path;
                    if ($this->content['source_file']['width'] > max($this->preference->image['size']))
                    {
                        $this->content['default_file']['width'] = max($this->preference->image['size']);
                        $this->content['default_file']['height'] = $this->content['source_file']['height'] / $this->content['source_file']['width'] * $this->content['default_file']['width'];
                    }
                    else
                    {
                        $this->content['default_file']['width'] = $this->content['source_file']['width'];
                        $this->content['default_file']['height'] = $this->content['source_file']['height'];
                    }
                    // Set default image quality as 'max'
                    $this->content['default_file']['quality'] = $this->preference->image['quality']['max'];

                }

                foreach ($this->request['extension'] as $extension_index=>$extension)
                {
                    // General Extensions
                    switch ($extension_index)
                    {
                        case 'minify':
                            $this->content['target_file']['minify'] = true;
                            break;
                    }
                    if ($this->request['file_type'] == 'image')
                    {
                        // Image Extensions
                        switch ($extension_index)
                        {
                            case 'image_size':
                                $this->content['target_file']['width'] = $this->preference->image['size'][$extension];
                                $this->content['target_file']['height'] = $this->content['source_file']['height'] / $this->content['source_file']['width'] * $this->content['target_file']['width'];
                                break;
                            case 'quality':
                                $this->content['target_file']['quality'] = $this->preference->image['quality'][$extension];
                                break;
                        }
                    }
                }

                // If image quality is not specified, use the fast generate setting
                if (!isset($this->content['target_file']['quality'])) $this->content['target_file']['quality'] = $this->preference->image['quality']['spd'];
                break;
            case 'data':
            default:
                $this->content['status'] = 'OK';
                $this->content['message'] = '';
                $this->content['field'] = array();
                $this->content['field']['base'] = URI_SITE_BASE;

                switch($this->request['module'])
                {
                    case 'members':
                        if (!isset($this->request['session_id']))
                        {
                            // Error Handling, session validation failed, session_id not set
                            $this->message->notice = 'Session ID Not Set, Redirect to Login Page';
                            $this->result['status'] = 301;
                            $this->result['header']['Location'] =  URI_SITE_BASE.'login';
                            return false;
                        }

                        $entity_api_session_obj = new entity_api_session();
                        $method_variable = ['status'=>'OK','message'=>'','api_session_id'=>$this->request['session_id'],'remote_ip'=>$this->request['remote_ip']];
                        $session = $entity_api_session_obj->validate_api_session_id($method_variable);
                        if ($session == false)
                        {
                            // Error Handling, session validation failed, session_id invalid
                            $this->message->notice = 'Session Validation Failed, Redirect to Login Page';
                            $this->result['status'] = 301;
                            $this->result['header']['Location'] =  URI_SITE_BASE.'login';
                            return false;
                        }
                        $entity_api_obj = new entity_api($session['account_id']);
                        if (empty($entity_api_obj->row))
                        {
                            // Error Handling, session validation failed, session_id is valid, but cannot read corresponding account
                            $this->message->error = 'Session Validation Succeed, but cannot find related account';
                            $this->result['status'] = 301;
                            $this->result['header']['Location'] =  URI_SITE_BASE.'login';
                            return false;
                        }
                        $this->content['account'] = end($entity_api_obj->row);

                        $this->result['cookie'] = ['session_id'=>['value'=>$session['name'],'time'=>strtotime($session['expire_time'])]];

                        $this->content['field']['robots'] = 'noindex, nofollow';

                        $this->content['field']['style'] = [
                            ['value'=>'/css/default.min.css','option'=>['format'=>'html_tag']]
                        ];

                        $this->content['field']['script'] = [
                            ['value'=>'/js/jquery.min.js','option'=>['source'=>PATH_CONTENT_JS.'jquery-1.11.3.js','format'=>'html_tag']],
                            ['value'=>'/js/default.min.js','option'=>['format'=>'html_tag']],
                        ];

                        $this->content['field']['name'] = ucwords($this->request['method']).' - '.$this->content['account']['name'];
                        $content = ['page_title'=>ucwords($this->request['method'])];
                        switch($this->request['method'])
                        {
                            case 'account':
                                $entity_account_obj = new entity_account($this->content['account']['id']);
                                $entity_contact_obj = new entity_contact($this->content['account']['id']);
                                $entity_profile_obj = new entity_profile($this->content['account']['id']);

                                break;
                            case 'listing':
                                break;
                            case 'credential':
                                $content['remote_ip'] = $this->request['remote_ip'];
                                $content['ajax_info'] = '';
                                $content['api_key_wrapper_class_extra'] = '';
                                $entity_api_key_obj = new entity_api_key();
                                $get_parameter = array(
                                    'bind_param' => array(':account_id'=>$this->content['account']['id']),
                                    'where' => array('`account_id` = :account_id')
                                );
                                $content['api_key'] = $entity_api_key_obj->get_api_key($get_parameter);
                                if (empty($content['api_key']))
                                {
                                    $content['ajax_info'] = 'No API Key Available, click "Create Credential" button to create one';
                                    $content['api_key_wrapper_class_extra'] = ' api_key_wrapper_empty';
                                }
                                $this->content['field']['content'] = render_html($content,'element_console_credential_body_container');
                                break;
                            case 'profile':
                                $content['account_name'] = $this->content['account']['name'];
                                $content['alternate_name_extra_class'] = '';
                                $content['alternate_name'] = $this->content['account']['alternate_name'];
                                if (empty($content['alternate_name']))
                                {
                                    $content['alternate_name_extra_class'] = ' inline_editor_text_empty';
                                }
                                $this->content['field']['content'] = render_html($content,'element_console_profile_body_container');
                                break;
                            case 'dashboard':
                            default:
                                $entity_api_method_obj = new entity_api_method('',['api_id'=>$this->content['account']['id']]);
                                $this->content['account']['api_method'] = $entity_api_method_obj->list_available_method();

                                $content['account_name'] = $this->content['account']['name'];
                                $content['api_method'] = $this->content['account']['api_method'];
                                $content['api_site_base'] = URI_SITE_BASE;
                                if (!empty($this->preference->api['force_ssl'])) $content['api_site_base'] = str_replace('http://','https://',$content['api_site_base']);
                                $this->content['field']['content'] = render_html($content,'element_console_dashboard_body_container');
                        }

                        break;
                    case 'default':
                    default:
                        // If page is login, check for user login session
                        if ($this->request['document'] == 'login')
                        {
                            if (isset($this->request['session_id']))
                            {
                                // session_id is set, check if it is already logged in
                                $entity_api_session_obj = new entity_api_session();
                                $method_variable = ['status'=>'OK','message'=>'','api_session_id'=>$this->request['session_id'],'remote_ip'=>$this->request['remote_ip']];
                                $session = $entity_api_session_obj->validate_api_session_id($method_variable);

                                if ($session === false)
                                {
                                    // If session_id is not valid, unset it and continue login process
                                    $this->result['cookie'] = ['session_id'=>['value'=>'','time'=>1]];
                                }
                                else
                                {
                                    $entity_api_obj = new entity_api($session['account_id']);
                                    if (empty($entity_api_obj->row))
                                    {
                                        // Error Handling, session validation succeed, session_id is valid, but cannot read corresponding account
                                        $this->message->error = 'Session Validation Succeed, but cannot find related api account';
                                        // If session_id is not valid, unset it and continue login process
                                        $this->result['cookie'] = ['session_id'=>['value'=>'','time'=>1]];
                                    }
                                    else
                                    {
                                        // If session is valid, redirect to console
                                        $this->result['cookie'] = ['session_id'=>['value'=>$session['name'],'time'=>strtotime($session['expire_time'])]];
                                        $this->result['status'] = 301;
                                        $this->result['header']['Location'] =  URI_SITE_BASE.'members/';

                                        return true;
                                    }
                                }
                            }
                            if ($_SERVER['REQUEST_METHOD'] == 'POST')
                            {
                                if (isset($this->request['option']['username']))
                                {
                                    $this->content['post_result'] = [
                                        'status'=>'OK',
                                        'message'=>''
                                    ];

                                    $login_param = [];
                                    $session_param = [];
                                    $login_param_keys = ['username','password','remember_me'];
                                    foreach($this->request['option'] as  $option_key=>&$option_value)
                                    {
                                        if (in_array($option_key,$login_param_keys))
                                        {
                                            $login_param[$option_key] = $option_value;
                                            //unset($option_value);
                                        }
                                        elseif ($option_key == 'complementary')
                                        {
                                            $complementary = base64_decode($option_value);
                                            if ($complementary === false OR $complementary == $option_value)
                                            {
                                                // Error Handling, complementary info error, complementary is not base64 encoded text
                                                $this->message->notice = 'Building: Login Failed';
                                                $this->content['post_result'] = [
                                                    'status'=>'REQUEST_DENIED',
                                                    'message'=>'Login Failed, please try again'
                                                ];
                                                $this->result['cookie'] = ['session_id'=>['value'=>'','time'=>1]];
                                            }
                                            else
                                            {
                                                $complementary_info = json_decode($complementary,true);
                                                if (empty($complementary_info))
                                                {
                                                    // Error Handling, complementary info not in json format
                                                    $this->message->notice = 'Building: Login Failed';
                                                    $this->content['post_result'] = [
                                                        'status'=>'REQUEST_DENIED',
                                                        'message'=>'Login Failed, please try again'
                                                    ];
                                                    $this->result['cookie'] = ['session_id'=>['value'=>'','time'=>1]];
                                                }
                                                else
                                                {
                                                    $session_param['remote_addr'] = $complementary_info['remote_addr'];
                                                    $session_param['http_user_agent'] = $complementary_info['http_user_agent'];
                                                }
                                            }
                                        }
                                    }

                                    if ($this->content['post_result']['status'] == 'OK')
                                    {
                                        $entity_api_obj = new entity_api();
                                        $api_account = $entity_api_obj->authenticate($login_param);
                                        if ($api_account === false)
                                        {
                                            // Error Handling, login failed
                                            $this->message->notice = 'Building: Login Failed';
                                            $this->content['post_result'] = [
                                                'status'=>'REQUEST_DENIED',
                                                'message'=>'Login Failed, invalid username or password'
                                            ];
                                            $this->result['cookie'] = ['session_id'=>['value'=>'','time'=>1]];
                                        }
                                        else
                                        {

                                            $session_expire = 86400;
                                            if (!empty($login_param['remember_me']))
                                            {
                                                $session_expire = $session_expire*30;
                                            }
                                            $entity_api_session_obj = new entity_api_session();
                                            $session_param = array_merge($session_param, ['account_id'=>$api_account['id'],'expire_time'=>gmdate('Y-m-d H:i:s',time()+$session_expire)]);
                                            $session = $entity_api_session_obj->generate_api_session_id($session_param);

                                            if (empty($session))
                                            {
                                                // Error Handling, create session id failed
                                                $this->message->error = 'Building: Fail to create session id';
                                                $this->content['post_result'] = [
                                                    'status'=>'REQUEST_DENIED',
                                                    'message'=>'Login Failed, fail to create new session'
                                                ];
                                            }
                                            else
                                            {
                                                $this->result['cookie'] = ['session_id'=>['value'=>$session['name'],'time'=>time()+$session_expire]];
                                                $this->result['status'] = 301;
                                                $this->result['header']['Location'] =  URI_SITE_BASE.'members';
                                            }
                                        }
                                    }
                                }
                                if ($this->content['post_result']['status'] != 'OK')
                                {
                                    // If login failed, show error message
                                    $this->content['field']['post_result_message'] = '<div class="ajax_info ajax_info_error">'.$this->content['post_result']['message']."</div>";
                                }

                                // Record login event
                                $entity_api_log_obj = new entity_api_log();
                                $log_record = ['name'=>'Login','remote_ip'=>$this->request['remote_ip'],'request_uri'=>$_SERVER['REQUEST_URI']];
                                $log_record = array_merge($log_record,$this->content['post_result']);
                                if (isset($api_account['id']))
                                {
                                    $log_record['account_id'] = $api_account['id'];
                                    $log_record['description'] =  $api_account['name'];
                                }
                                else
                                {
                                    $log_record['description'] =  $this->request['option']['username'];
                                }
                                if (isset($session['name'])) $log_record['content'] = $session['name'];
                                $entity_api_log_obj->set_log($log_record);
                            }
                        }

                        if ($this->request['document'] == 'logout')
                        {
                            // success or fail, logout page always redirect to login page after process complete
                            $this->result['status'] = 301;
                            $this->result['header']['Location'] =  URI_SITE_BASE.'login';
                            if (!isset($this->request['session_id']))
                            {
                                // session_id is not set, redirect to login page
                                return true;
                            }
                            $this->result['cookie'] = ['session_id'=>['value'=>'','time'=>1]];

                            $entity_api_session_obj = new entity_api_session();
                            $get_parameter = array(
                                'bind_param' => array(':name'=>$this->request['session_id']),
                                'where' => array('`name` = :name')
                            );
                            $entity_api_session_obj->get($get_parameter);
                            /*$method_variable = ['status' => 'OK', 'message' => '', 'api_session_id' => $this->request['session_id']];
                            $session = $entity_api_session_obj->validate_api_session_id($method_variable);
                            if ($session === false)
                            {
                                // If session_id is not valid, redirect to login page
                                return true;
                            }*/
                            if (count($entity_api_session_obj->row) > 0)
                            {
                                // Record logout event
                                $session_record = end($entity_api_session_obj->row);

                                $entity_api_log_obj = new entity_api_log();
                                $log_record = ['name'=>'Logout','account_id'=>$session_record['account_id'],'status'=>'OK','message'=>'Session close by user','content'=>$session_record['name'],'remote_ip'=>$this->request['remote_ip'],'request_uri'=>$_SERVER['REQUEST_URI']];
                                $entity_api_obj = new entity_api($session_record['account_id']);
                                if (count($entity_api_obj->row) > 0)
                                {
                                    $log_record['description'] = end($entity_api_obj->row)['name'];
                                }
                                $entity_api_log_obj->set_log($log_record);
                            }

                            // If session is valid, delete the session then redirect to login
                            $entity_api_session_obj->delete();
                            return true;
                        }

                        if (isset($this->request['option']['field']))
                        {
                            $this->content['field'] = array_merge($this->content['field'],$this->request['option']['field']);
                        }
                        else
                        {
                            // Set field value from database
                            if (!isset($this->request['document']))
                            {
                                $this->result['status'] = 404;
                                return false;
                            }
                            $page_obj = new view_web_page($this->request['document']);
                            if (empty($page_obj->id_group))
                            {
                                //$this->result['status'] = 404;
                                $this->result['status'] = 301;
                                $this->result['header']['Location'] =  URI_SITE_BASE.'login';
                                return false;
                            }
                            if (count($page_obj->id_group) > 1)
                            {
                                // Error Handling, ambiguous reference, multiple page found, database data error
                                $GLOBALS['global_message']->warning = __FILE__.'(line '.__LINE__.'): Multiple web page resources loaded '.implode(',',$page_obj->id_group);
                            }
                            $page_fetched_value = $page_obj->fetch_value(['page_size'=>1]);
                            if (empty($page_fetched_value))
                            {
                                // Error Handling, fetch record row failed, database data error
                                $GLOBALS['global_message']->error = __FILE__.'(line '.__LINE__.'): Fetch row failed '.implode(',',$page_obj->id_group);
                                $this->result['status'] = 404;
                                return false;
                            }
                            $this->content['field'] = array_merge($this->content['field'],end($page_fetched_value));
                            $this->content['field']['style'] = [
                                //['value'=>'/css/default.min.css','option'=>['format'=>'html_tag']]
                            ];

                            $this->content['field']['script'] = [
                                ['value'=>'/js/jquery.min.js','option'=>['source'=>PATH_CONTENT_JS.'jquery-1.11.3.js','format'=>'html_tag']]
                                //['value'=>'/js/default.min.js','option'=>['format'=>'html_tag']]
                                //['value'=>'/js/default-top4.js','option'=>['source'=>'http://dev.top4.com.au/scripts/default.js','format'=>'html_tag']]
                            ];

                            if ($this->request['document'] == 'login' OR $this->request['document'] == 'signup' )
                            {
                                $this->content['field']['complementary'] = base64_encode(json_encode(['remote_addr'=>get_remote_ip(), 'http_user_agent'=>$_SERVER['HTTP_USER_AGENT'], 'submission_id'=>sha1(openssl_random_pseudo_bytes(5))]));
                            }
                        }
                }

                if (isset($this->request['option']['template']))
                {
                    $this->content['template'] = $this->request['option']['template'];
                }
                else
                {
                    // Looking for default template
                    $template_name_part = [];
                    if (!empty($this->request['module'])) $template_name_part[] = $this->request['module'];
                    else $template_name_part[] = 'default';
                    if (isset($this->request['method'])) $template_name_part[] = $this->request['method'];
                    if (isset($this->request['document'])) $template_name_part[] = $this->request['document'];

                    $default_css = array();
                    $default_js = array();

                    while (!empty($template_name_part))
                    {
                        if (file_exists(PATH_CONTENT_CSS.implode('_',$template_name_part).'.css'))
                        {
                            array_unshift($default_css, ['value'=>'/css/'.implode('_',$template_name_part).'.min.css','option'=>['format'=>'html_tag']]);
                        }
                        if (file_exists(PATH_CONTENT_JS.implode('_',$template_name_part).'.js'))
                        {
                            array_unshift($default_js, ['value'=>'/js/'.implode('_',$template_name_part).'.min.js','option'=>['format'=>'html_tag']]);
                        }
                        if (!isset($this->content['template']) AND file_exists(PATH_TEMPLATE.'page_'.implode('_',$template_name_part).FILE_EXTENSION_TEMPLATE))
                        {
                            $this->content['template'] = 'page_'.implode('_',$template_name_part);
                        }
                        array_pop($template_name_part);
                    }

                    $this->content['field']['style'] = array_merge($this->content['field']['style'],$default_css);
                    $this->content['field']['script'] = array_merge($this->content['field']['script'],$default_js);
                    if (!isset($this->content['template'])) $this->content['template'] = 'page_default';
                }
                $this->result['content'] = render_html($this->content['field'],$this->content['template']);


                return true;
        }

        return true;
    }

    private function generate_rendering()
    {
        switch($this->content['format'])
        {
            case 'css':
            case 'js':
                $target_file_path = dirname($this->content['target_file']['path']);
                if (!file_exists($target_file_path)) mkdir($target_file_path, 0755, true);

                if (!file_exists($this->content['target_file']['path']) OR $this->content['source_file']['last_modified'] > $this->content['target_file']['last_modified'])
                {
                    if (!empty($this->content['target_file']['minify']))
                    {
                        // Yuicompressor 2.4.8 does not support output as Windows absolute path start with Driver
                        $start_time = microtime(true);
                        $execution_command = 'java -jar '.PATH_CONTENT_JAR.'yuicompressor-2.4.8.jar --type '.$this->content['format'].' "'.$this->content['source_file']['path'].'" -o "'.preg_replace('/^\w:/','',$this->content['target_file']['path']).'"';
                        exec($execution_command, $result);
                        $this->message->notice = 'Yuicompressor Execution Time: '. (microtime(true) - $start_time);
                        $this->message->notice = $execution_command;
                        //$this->message->notice = $result;
                    }

                    if (!file_exists($this->content['target_file']['path']))
                    {
                        // If fail to generate minimized file, copy the source file
                        copy($this->content['source_file']['path'], $this->content['target_file']['path']);
                    }
                    else
                    {
                        if (filesize($this->content['target_file']['path']) > $this->content['source_file']['content_length'])
                        {
                            // If file getting bigger, original file probably already minimized with better algorithm (e.g. google's js files, just use the original file)
                            copy($this->content['source_file']['path'], $this->content['target_file']['path']);
                        }
                    }
                    if (!empty($this->content['target_file']['minify']))
                    {
                        $start_time = microtime(true);
                        file_put_contents($this->content['target_file']['path'],minify_content(file_get_contents($this->content['target_file']['path']),$this->request['file_type']));
                        $this->message->notice = 'PHP Minifier Execution Time: '. (microtime(true) - $start_time);
                    }
                    touch($this->content['target_file']['path'],$this->content['source_file']['last_modified']);
                }

                if (!file_exists($this->content['target_file']['path']))
                {
                    // Error Handling, Fail to generate target file
                    $this->message->error = 'Rendering: Fail to generate target file';
                    return false;
                }

                if ($this->request['file_uri'] != $this->content['target_file']['uri'])
                {
                    // On Direct Rendering from HTTP REQUEST, if request_uri is different from target file_uri, do 301 redirect
                    $this->result['status'] = 301;
                    $this->result['header']['Location'] = str_replace(URI_SITE_BASE,'/',$this->content['target_file']['uri']);
                    return false;
                }

                // Try up to 10 times to delete the source file
                $unlink_retry_counter = 10;
                while (!unlink($this->content['source_file']['path']) AND $unlink_retry_counter > 0)
                {
                    sleep(1);
                    $unlink_retry_counter--;
                }

                $this->content['target_file']['last_modified'] = filemtime($this->content['target_file']['path']);
                $this->content['target_file']['content_length'] = filesize($this->content['target_file']['path']);

                if ($this->content['target_file']['content_length'] == 0)
                {
                    // Error Handling, Fail to generate target file
                    $this->message->error = 'Rendering: Fail to generate target file';
                    return false;
                }

                $this->result['header']['Last-Modified'] = gmdate('D, d M Y H:i:s',$this->content['target_file']['last_modified']).' GMT';
                $this->result['header']['Content-Length'] = $this->content['target_file']['content_length'];

                switch ($this->request['file_type'])
                {
                    case 'css':
                        $this->result['header']['Content-Type'] = 'text/css';
                        break;
                    case 'js':
                        $this->result['header']['Content-Type'] = 'application/javascript';
                        break;
                    default:
                }

                $this->result['file_path'] = $this->content['target_file']['path'];
                break;
            case 'file_uri':
                $this->result['content'] = $this->content['target_file']['uri'];
                break;
            case 'image':
                // create source file resource object
                switch ($this->content['source_file']['content_type']) {
                    case 'image/png':
                        $source_image = imagecreatefrompng($this->content['source_file']['path']);
                        break;
                    case 'image/gif':
                        $source_image = imagecreatefromgif($this->content['source_file']['path']);
                        break;
                    case 'image/jpg':
                    case 'image/jpeg':
                        $source_image = imagecreatefromjpeg($this->content['source_file']['path']);
                        break;
                    default:
                        $source_image = imagecreatefromstring($this->content['source_file']['path']);
                }
                if ($source_image === FALSE) {
                    // Error Handling, fail to create image
                    $this->message->error = 'Rendering: fail to create image';
                    return false;
                }

                // If the source file is not copied from default file, generate default file
                if (!isset($this->content['source_file']['original_file']) OR $this->content['source_file']['original_file'] != $this->content['default_file']['path'])
                {
                    if ($this->content['source_file']['width'] != $this->content['default_file']['width'])
                    {
                        // Resize the image if it is not the same size
                        $default_image = imagecreatetruecolor($this->content['default_file']['width'],  $this->content['default_file']['height']);
                        imagecopyresampled($default_image,$source_image,0,0,0,0,$this->content['default_file']['width'], $this->content['default_file']['height'],$this->content['source_file']['width'],$this->content['source_file']['height']);
                    }
                    else
                    {
                        $default_image = $source_image;
                    }
                    imageinterlace($default_image,true);

                    // Save Default Image with Max Quality Set
                    $image_quality = $this->content['default_file']['quality'];
                    switch($this->content['source_file']['content_type'])
                    {
                        case 'image/png':
                            imagesavealpha($default_image, true);
                            imagepng($default_image, $this->content['default_file']['path'], $image_quality['image/png'][0], $image_quality['image/png'][1]);
                            break;
                        case 'image/gif':
                            // If source is gif, directly copy it, any resize will lose frame data (lose animation effect)
                            copy($this->content['source_file']['path'],$this->content['default_file']['path']);
                            $this->content['default_file']['width'] = $this->content['source_file']['width'];
                            $this->content['default_file']['height'] = $this->content['source_file']['height'];
                            break;
                        case 'image/jpg':
                        case 'image/jpeg':
                        default:
                            imagejpeg($default_image, $this->content['default_file']['path'], $image_quality['image/jpeg']);
                    }
                    $this->content['default_file']['content_length'] = filesize($this->content['default_file']['path']);
                    if ($this->content['default_file']['content_length'] > $this->content['source_file']['content_length'])
                    {
                        // If somehow resized image getting bigger in size, just overwrite it with original file
                        copy($this->content['source_file']['path'],$this->content['default_file']['path']);
                        $this->content['default_file']['content_length'] = $this->content['source_file']['content_length'];
                    }
                    touch($this->content['default_file']['path'],$this->content['source_file']['last_modified']);
                    $this->content['default_file']['last_modified'] = $this->content['source_file']['last_modified'];
                    // Default image create process finish here, unset default file gd object
                    unset($default_image);
                }

                // If the required file is not the default file, generate the required file
                if ($this->content['default_file']['path'] != $this->content['target_file']['path'])
                {
                    if ($this->content['source_file']['width'] != $this->content['target_file']['width'])
                    {
                        // Resize the image if it is not the same size
                        $target_image = imagecreatetruecolor($this->content['target_file']['width'],  $this->content['target_file']['height']);
                        imagecopyresampled($target_image,$source_image,0,0,0,0,$this->content['target_file']['width'], $this->content['target_file']['height'],$this->content['source_file']['width'],$this->content['source_file']['height']);
                    }
                    else
                    {
                        $target_image = $source_image;
                    }
                    imageinterlace($target_image,true);

                    // Save Default Image with Max Quality Set
                    $image_quality = $this->content['target_file']['quality'];
                    switch($this->content['source_file']['content_type'])
                    {
                        case 'image/png':
                            imagesavealpha($target_image, true);
                            imagepng($target_image, $this->content['target_file']['path'], $image_quality['image/png'][0], $image_quality['image/png'][1]);
                            $this->content['target_file']['content_type'] = 'image/png';
                            break;
                        case 'image/gif':
                        case 'image/jpg':
                        case 'image/jpeg':
                        default:
                            imagejpeg($target_image, $this->content['target_file']['path'], $image_quality['image/jpeg']);
                            $this->content['target_file']['content_type'] = 'image/jpeg';
                    }
                    $this->content['target_file']['content_length'] = filesize($this->content['target_file']['path']);
                    touch($this->content['target_file']['path'],$this->content['source_file']['last_modified']);
                    $this->content['target_file']['last_modified'] = $this->content['source_file']['last_modified'];

                    // Default image create process finish here, unset default file gd object
                    unset($target_image);
                }
                if (empty($this->content['target_file']['last_modified'])) $this->content['target_file']['last_modified'] = filemtime($this->content['target_file']['path']);

                // Try up to 3 times to delete the source file
                $unlink_retry_counter = 3;
                while (!unlink($this->content['source_file']['path']) AND $unlink_retry_counter > 0)
                {
                    sleep(1);
                    $unlink_retry_counter--;
                }

                $this->result['header']['Last-Modified'] = gmdate('D, d M Y H:i:s',$this->content['target_file']['last_modified']).' GMT';
                $this->result['header']['Content-Length'] = $this->content['target_file']['content_length'];
                $this->result['header']['Content-Type'] = $this->content['target_file']['content_type'];

                $this->result['file_path'] = $this->content['target_file']['path'];
                break;
            case 'json':
                $this->result['content'] = json_encode($this->content['api_result']);
                $this->result['header']['Last-Modified'] = gmdate('D, d M Y H:i:s').' GMT';
                $this->result['header']['Content-Length'] = strlen($this->result['content']);
                $this->result['header']['Content-Type'] = 'application/json';
                break;
            case 'html_tag':
                $this->result['content'] = '<'.$this->content['html_tag']['name'];
                foreach($this->content['html_tag']['attr'] as $attr_name=>$attr_content)
                {
                    $this->result['content'] .= ' '.$attr_name.'="'.$attr_content.'"';
                }
                $this->result['content'] .= '>';
                $void_tag = ['area','base','br','col','command','embed','hr','img','input','keygen','link','meta','param','source','track','wbr'];
                if (!in_array($this->content['html_tag']['name'],$void_tag))
                {
                    if (isset($this->content['html_tag']['html']))
                    {
                        $this->result['content'] .= htmlspecialchars($this->content['html_tag']['html']);
                    }
                    $this->result['content'] .= '</'.$this->content['html_tag']['name'].'>';
                }
                break;
            case 'xml':
                $this->result['content'] = render_xml($this->content['api_result'])->asXML();
                $this->result['header']['Last-Modified'] = gmdate('D, d M Y H:i:s').' GMT';
                $this->result['header']['Content-Length'] = strlen($this->result['content']);
                $this->result['header']['Content-Type'] = 'text/xml';
                break;
            case 'html':
                if (!isset($this->content['field'])) $this->content['field'] = array();
                if (!isset($this->content['template'])) $this->content['template'] = '';
                $this->result['content'] = render_html($this->content['field'],$this->content['template']);
                $this->result['header']['Last-Modified'] = gmdate('D, d M Y H:i:s').' GMT';
                $this->result['header']['Content-Length'] = strlen($this->result['content']);
                $this->result['header']['Content-Type'] = 'text/html';
                break;
        }
    }

    function render()
    {
        session_start();
        if (isset($this->result['cookie']))
        {
            foreach($this->result['cookie'] as $cookie_name=>$cookie_content)
            {
                setcookie($cookie_name,$cookie_content['value'],$cookie_content['time'],'/'.(FOLDER_SITE_BASE != ''?(FOLDER_SITE_BASE.'/'):''));
            }
        }

        http_response_code($this->result['status']);
        foreach($this->result['header'] as $header_name=>$header_content)
        {
            header($header_name.': '.$header_content);
        }
        if (isset($this->result['file_path']))
        {
            readfile($this->result['file_path']);
            exit();
        }
        if (!empty($this->result['content']))
        {
            print_r($this->result['content']);
        }
    }

    function get_result()
    {
        if (isset($this->result['file_path']))
        {
            return file_get_contents($this->result['file_path']);
        }
        return $this->result['content'];
    }
}
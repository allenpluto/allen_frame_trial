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

        $this->request = array('force_consistent_uri'=>true);
        $this->result = array(
            'status'=>200,
            'header'=>array(),
            'content'=>''
        );

        // Analyse uri structure and raw environment variables, store into $this->request
        if ($this->request_decoder($parameter) === false)
        {
            // Error Log, error during reading input uri and parameters
            $this->message->error = 'Fail: Error during request_decoder';
        }
        $this->time_stack['request_decoder'] = microtime(1);

        // Generate the necessary components for the content, store separate component parts into $content
        // Read data from database (if applicable), only generate raw data from db
        // If any further complicate process required, leave it to render
        if ($this->result['status'] == 200 AND $this->build_content() === false)
        {
            // Error Log, error during building data object
            $this->message->error = 'Fail: Error during build_content';
        }
        $this->time_stack['build_content'] = microtime(1);
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
        $this->time_stack['generate_rendering'] = microtime(1);
//print_r('generate_rendering: <br>');
//print_r(filesize($this->content['target_file']['path']));
//print_r($this);
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

        $option_preset = ['document','file_type','file_extension','file_extra_extension','module','template','action'];
        foreach($option as $key=>$item)
        {
            // Options from GET, POST overwrite ones decoded from uri
            if (in_array($key,$option_preset))
            {
                $this->request[$key] = $item;
                unset($option[$key]);
            }
        }
        $option_unset = ['asset_redirect','request_uri','rewrite_base','final_request'];
        foreach($option as $key=>$item)
        {
            // Options from GET, POST overwrite ones decoded from uri
            if (in_array($key,$option_unset))
            {
                unset($option[$key]);
            }
        }
        // dump the rest custom/unrecognized input variables into $request['option']
        $this->request['option'] = $option;
        unset($option_preset);
        unset($option);

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
                $request_path_part = array_shift($request_path);
            }
            else
            {
                $this->request['source_type'] = 'data';
                if(!isset($this->request['file_type'])) $this->request['file_type'] = 'html';
            }
            if ($this->request['source_type'] == 'data')
            {
                $this->request['file_uri'] = URI_SITE_BASE;
            }
            else
            {
                $this->request['file_uri'] = URI_ASSET.$this->request['file_type'].'/';
            }
            if (!isset($this->request['file_extension'])) $this->request['file_extension'] = $this->request['file_type'];
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
                if (!empty($file_part)) $this->request['file_extension'] = array_pop($file_part);
                $this->request['file_extra_extension'] = [];

                if (is_array($this->preference->{$this->request['file_type']}))
                {
                    foreach ($this->preference->{$this->request['file_type']} as $file_option_name=>$file_option_value)
                    {
                        $file_option = array();
                        $file_option = array_keys($file_option_value);
                        foreach ($file_part as $file_extension_index=>$file_extension)
                        {
                            if (in_array($file_extension, $file_option)) {
                                $this->request['file_extra_extension'][$file_option_name] = $file_extension;
                                unset($file_part[$file_extension_index]);
                                break;
                            }
                        }
                        unset($file_option);
                    }
                }
                if (!empty($file_part))
                {
                    // Put the rest part that is not an extension back to document name, e.g. jquery-1.11.8.min.js
                    $this->request['document'] .= '.'.implode('.',$file_part);
                }
                unset($file_part);
                $decoded_file_name = $this->request['document'];
                if (!empty($this->request['file_extra_extension'])) $decoded_file_name .= '.'.implode('.',$this->request['file_extra_extension']);
                if (!empty($this->request['file_extension'])) $decoded_file_name .= '.'.$this->request['file_extension'];

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
                $this->request['file_uri'] .= $file_name;

                if (preg_match('/-(\d*)$/',$this->request['document']))
                {
                    // images have special directory structure, images loaded from database real storage path is constructed by id
                    $file_name_parts = explode('-',$this->request['document']);
                    $file_id = array_pop($file_name_parts);
                    $this->request['file_id'] = $file_id;
                    $sub_folder = array();
                    do
                    {
                        $sub_image_id = $file_id % 1000;
                        array_unshift($sub_folder, $sub_image_id);
                        $file_id = floor($file_id / 1000);
                    } while ($file_id >= 1);
                    foreach ($sub_folder as $index=>&$sub_image_id)
                    {
                        if ($index != 0)
                        {
                            $sub_image_id = str_repeat('0', 3-strlen($sub_image_id)).$sub_image_id;
                        }
                    }
                    $this->request['file_path'] = $this->request['file_path'].implode(DIRECTORY_SEPARATOR,$sub_folder).DIRECTORY_SEPARATOR.$file_name;
                }
                else
                {
                    $this->request['file_path'] .= $file_name;
                }
                unset($file_name);
                break;
            case 'data':
            default:
                $control_panel = ['members','sitemgr'];
                if (in_array($request_path_part,$control_panel))
                {
                    $this->request['control_panel'] = $request_path_part;
                    $request_path_part = array_shift($request_path);
                }
                else
                {
                    $this->request['control_panel'] = '';
                }

                //$request_path_part = array_shift($request_path);
                $module = ['account','article','profile','listing','business','business-amp','gallery'];
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
                    case 'account':
                        $method = ['change_password','edit',''];
                        if (in_array($request_path_part,$method))
                        {
                            $this->request['method'] = $request_path_part;
                            $request_path_part = array_shift($request_path);
                        }
                        else
                        {
                            $this->request['method'] = end($method);
                        }
                        break;
                    case 'article':
                        $method = ['detail','guide','search',''];
                        if (in_array($request_path_part,$method))
                        {
                            $this->request['method'] = $request_path_part;
                            $request_path_part = array_shift($request_path);
                        }
                        else
                        {
                            $this->request['method'] = end($method);
                        }
                        switch($this->request['method'])
                        {
                            case 'detail':
                                $request_path_part = array_shift($request_path);
                                if (!empty($request_path_part))
                                {
                                    $this->request['document'] = $request_path_part;
                                }
                                else
                                {
                                    $this->request['method'] = '';
                                }
                                break;
                            case 'guide':
                                $request_path_part = array_shift($request_path);
                                if (!empty($request_path_part))
                                {
                                    $this->request['option']['category'] = $request_path_part;
                                }
                                else
                                {
                                    $this->request['method'] = '';
                                }
                                break;
                            case 'search':
                                $request_path_part = array_shift($request_path);
                                if (!empty($request_path_part))
                                {
                                    $this->request['option']['keywords'] = $request_path_part;
                                }
                                else
                                {
                                    $this->request['method'] = '';
                                }
                                break;
                            default:
                        }
                        break;
                    case 'gallery':
                        $method = ['add','delete','edit',''];
                        if (in_array($request_path_part,$method))
                        {
                            $this->request['method'] = $request_path_part;
                            $request_path_part = array_shift($request_path);
                        }
                        else
                        {
                            $this->request['method'] = end($method);
                        }
                        break;
                    case 'listing':
                        $method = ['search','find','edit','update','gallery',''];
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
                                            $this->message->error = __FILE__.'(line '.__LINE__.'): Construction Fail, unknown option ['.$request_path[$i*2].'] for '.$this->request['module'].'/'.$this->request['method'];
                                            break 2;
                                        }
                                        $this->request['option'][$request_path[$i*2]] = $request_path[$i*2+1];
                                    }
                                }
                                break;
                            case 'find':
                                if (empty($request_path_part))
                                {
                                    // Error Handling, category not provided
                                    $GLOBALS['global_message']->error = __FILE__.'(line '.__LINE__.'): category not set';
                                    $this->result['status'] = 404;
                                    $this->result['content'] = 'Category not set';
                                    return false;
                                }
                                $this->request['method'] = 'search';
                                $this->request['force_consistent_uri'] = false;
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
                                            $this->message->error = __FILE__.'(line '.__LINE__.'): Construction Fail, unknown option ['.$request_path[$i*2].'] for '.$this->request['module'].'/'.$this->request['method'];
                                            break 2;
                                        }
                                        $this->request['option'][$request_path[$i*2]] = $request_path[$i*2+1];
                                    }
                                }
                                break;
                            default:
                                //TODO: control panel methods validation, most methods would need $this->request['option']['id']
                                //$this->request['document'] = $request_path_part;
                        }

                        break;
                    default:
                        $this->request['document'] = $request_path_part;
                }
                if (!isset($this->request['method']))
                {
                    $this->request['method'] = '';
                }
                if (!isset($this->request['action']))
                {
                    $this->request['action'] = '';
                }

                if (!empty($this->request['control_panel']))
                {
                    $this->request['file_uri'] .= $this->request['control_panel'].'/';
                }

                if (!empty($this->request['module']))
                {
                    $this->request['file_path'] .= $this->request['module'].DIRECTORY_SEPARATOR;
                    $this->request['file_uri'] .= $this->request['module'].'/';
                }

                if (!empty($this->request['method']))
                {
                    $this->request['file_path'] .= $this->request['method'].DIRECTORY_SEPARATOR;
                    $this->request['file_uri'] .= $this->request['method'].'/';
                }

//                if (!empty($this->request['action']))
//                {
//                    $this->request['file_path'] .= $this->request['action'].DIRECTORY_SEPARATOR;
//                    $this->request['file_uri'] .= $this->request['action'];
//                }

                if (!empty($this->request['document']))
                {
                    $this->request['file_path'] .= $this->request['document'].DIRECTORY_SEPARATOR;
                    $this->request['file_uri'] .= $this->request['document'];
                }

                $this->request['file_path'] .= 'index.'.$this->request['file_extension'];


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

        if ($this->request['force_consistent_uri'] AND parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) != parse_url($this->request['file_uri'],PHP_URL_PATH))
        {
            if ($this->request['file_type'] == 'html')
            {
                $this->message->notice = 'Redirect - request uri ['.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH).'] is different from decoded uri ['.parse_url($this->request['file_uri'],PHP_URL_PATH).']';
                $this->result['status'] = 301;
                $this->result['header']['Location'] =  $this->request['file_uri'].(!empty($this->request['option'])?('?'.http_build_query($this->request['option'])):'');
            }
        }
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
                    $this->message->notice = 'Session Validation Succeed, but cannot find related account';
                }
                else
                {
                    $this->content['account'] = end($entity_account_obj->row);
                    $this->content['session'] = $session;
                }
                unset($entity_account_obj);
            }
            unset($session);
        }

        // If auth_key is set, try to get account from auth_key
        if (!empty($this->request['auth_key']))
        {
            $entity_account_key_obj = new entity_account_key();
            $method_variable = ['account_key'=>$this->request['auth_key'],'remote_ip'=>$this->request['remote_ip']];
            $auth_id = $entity_account_key_obj->validate_account_key($method_variable);
            if ($auth_id === false)
            {
                // Error Handling, Account key authentication failed
                $this->message->notice = 'Building: Account Key Authentication Failed';
                $this->content['api_result'] = [
                    'status'=>$method_variable['status'],
                    'message'=>$method_variable['message']
                ];
            }
            else
            {
                $entity_account_obj = new entity_account($this->content['account']['id']);
                if (empty($entity_account_obj->row))
                {
                    // Error Handling, session validation failed, account_key is valid, but cannot read corresponding account
                    $this->message->error = 'Account Key Authentication Succeed, but cannot find related account';
                    $this->content['account_result'] = [
                        'status'=>'REQUEST_DENIED',
                        'message'=>'Cannot get account info, it might be suspended or temporarily inaccessible'
                    ];
                }
                else
                {
                    $this->content['account'] = end($entity_account_obj->row);
                }
                unset($entity_account_obj);
            }
            unset($auth_id);
        }

        // Regularize Content output format
        if (!isset($this->request['option']['format'])) $this->content['format'] = $this->request['file_type'];
        else $this->content['format'] = $this->request['option']['format'];

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

//                $file_relative_path = $this->request['file_type'].DIRECTORY_SEPARATOR;
//                if (!empty($this->request['sub_path'])) $file_relative_path .= implode(DIRECTORY_SEPARATOR,$this->request['sub_path']).DIRECTORY_SEPARATOR;
//                $this->content['source_file'] = [
//                    'path' => PATH_ASSET.$file_relative_path.$this->request['document'].'.src.'.$this->request['file_extension'],
//                    'source' => 'local_file'
//                ];
//                $source_file_relative_path =  $file_relative_path .  $this->request['document'].'.src.'.$this->request['file_extension'];
//                $file_relative_path .= $this->request['document'].'.'.$this->request['file_extension'];

                $this->content['source_file'] = [
                    'path' => dirname($this->request['file_path']).DIRECTORY_SEPARATOR.$this->request['document'].'.'.$this->request['file_extension'],
                    'source' => 'local_file'
                ];

                if (isset($this->request['option']['source']))
                {
                    if (preg_match('/^http/',$this->request['option']['source']) == 1)
                    {
                        if (strpos($this->request['option']['source'],URI_SITE_BASE) == FALSE)
                        {
                            // If source_file is not relative uri and not start with current site uri base, it is an external (cross domain) source file
                            $this->content['source_file']['original_file'] = $this->request['option']['source'];
                            $this->content['source_file']['source'] = 'remote_file';
                        }
                        else
                        {
                            // If source_file is in local server, but reference by uri rather than path, decode recursively
                            $source_content = new content(str_replace(URI_SITE_BASE,'',$this->request['option']['source']));
                            $source_file_path = $source_content->request['file_path'];
                            unset($source_content);

                            if (!file_exists($source_file_path))
                            {
                                $this->message->error = 'Building: source file does not exist '.$source_file_path;
                                $this->result['status'] = 404;
                                return false;
                            }
                            $this->content['source_file']['original_file'] = $source_file_path;
                        }
                    }
                    else
                    {
                        // source file not leading by http is assumed to be local file path
                        if (!file_exists($this->request['option']['source']))
                        {
                            $this->message->error = 'Building: source file does not exist '.$this->request['option']['source'];
                            $this->result['status'] = 404;
                            return false;
                        }
                        $this->content['source_file']['original_file'] = $this->request['option']['source'];
                    }

                    if ($this->content['source_file']['source'] == 'remote_file')
                    {
                        // External source file
                        $file_header = @get_headers($this->content['source_file']['original_file'],true);
                        if (strpos( $file_header[0], '200 OK' ) === false)
                        {
                            // Error Handling, fail to get external source file header
                            $this->message->error = 'Source File not accessible - '.$file_header[0];
                            return false;
                        }
                        if (isset($file_header['Last-Modified']))
                        {
                            $this->content['source_file']['last_modified'] = strtotime($file_header['Last-Modified']);
                        }
                        else
                        {
                            if (isset($file_header['Expires']))
                            {
                                $this->content['source_file']['last_modified'] = strtotime($file_header['Expires']);
                            }
                            else
                            {
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
                    else
                    {
//                        $this->content['source_file']['content_length'] = filesize($this->content['source_file']['original_file']);
//                        $this->content['source_file']['last_modified'] = filemtime($this->content['source_file']['original_file']);
//                        $this->content['source_file']['content_type'] = mime_content_type($this->content['source_file']['original_file']);
                        $this->content['source_file']['path'] = $this->content['source_file']['original_file'];
                    }
                }
                else
                {
                    if (file_exists($this->content['source_file']['path']))
                    {
//                        $this->content['source_file']['content_length'] = filesize($this->content['source_file']['path']);
//                        $this->content['source_file']['last_modified'] = filemtime($this->content['source_file']['path']);
                    }
                    elseif (file_exists(str_replace(PATH_ASSET,PATH_CONTENT,$this->content['source_file']['path'])))
                    {
                        // If source file does not exist in asset folder, check if there is a corresponding file in content folder
                        $this->content['source_file']['path'] = str_replace(PATH_ASSET,PATH_CONTENT,$this->content['source_file']['path']);
                    }
                    else
                    {
                        // If file source doesn't exist in content folder, try database
                        $view_class = 'view_'.$this->request['file_type'];
                        if (!class_exists($view_class))
                        {
                            // Error Handling, view class does not exist for given file type
                            $this->message->error = 'Building: cannot find source file - view class does not exist';
                            $this->result['status'] = 404;
                            return false;
                        }
                        switch ($this->request['file_type'])
                        {
                            case 'image':
                                if (empty($this->request['file_id']))
                                {
                                    // Error Handling, fail to get source file from database, last part of file name is not a valid id
                                    $this->message->error = 'Building: cannot find source file';
                                    $this->result['status'] = 404;
                                    return false;
                                }

                                $view_obj = new $view_class($this->request['file_id']);
                                break;
                            default:
                                print_r($this);exit;
                        }

                        if (!empty($view_obj->id_group))
                        {
                            $file_record = $view_obj->fetch_value();
                            $file_record = end($file_record);
                        }

                        if (empty($file_record['file_path']) OR !file_exists($file_record['file_path']))
                        {
                            $this->message->error = 'Building: cannot find source file - file does not exist';
                            $this->result['status'] = 404;
                            return false;
//                            $entity_class = 'entity_'.$this->request['file_type'];
//                            if (!class_exists($entity_class))
//                            {
//                                // Error Handling, last ditch failed, source file does not exist in database either
//                                $this->message->error = 'Building: cannot find source file';
//                                $this->result['status'] = 404;
//                                return false;
//                            }
//                            $entity_obj = new $entity_class($this->request['file_id']);
//                            if (empty($entity_obj->row))
//                            {
//                                // Error Handling, fail to get source file from database, cannot find matched record
//                                $this->message->error = 'Building: fail to get source file from database, invalid id';
//                                $this->result['status'] = 404;
//                                return false;
//                            }
//                            $entity_obj->sync(['sync_type'=>'update_current']);
//
//                            $view_obj->get();
//                            if (!empty($view_obj->id_group))
//                            {
//                                $file_record = end($view_obj->fetch_value());
//                            }
                        }

                        if (isset($file_record['file_size'])) $this->content['source_file']['content_length'] = $file_record['file_size'];
                        if (isset($file_record['update_time'])) $this->content['source_file']['last_modified'] = strtotime($file_record['update_time']);
                        if (isset($file_record['mime'])) $this->content['source_file']['content_type'] = $file_record['mime'];
                        if (isset($file_record['width'])) $this->content['source_file']['width'] = $file_record['width'];
                        if (isset($file_record['height'])) $this->content['source_file']['height'] = $file_record['height'];
                    }
                }

                if(!isset($this->content['source_file']['content_length'])) $this->content['source_file']['content_length'] = filesize($this->content['source_file']['path']);
                if(!isset($this->content['source_file']['last_modified'])) $this->content['source_file']['last_modified'] = filemtime($this->content['source_file']['path']);
                if(!isset($this->content['source_file']['content_type'])) $this->content['source_file']['content_type'] = mime_content_type($this->content['source_file']['path']);


                if ($this->content['target_file']['path'] == $this->content['source_file']['path'])
                {
                    if (isset($this->content['source_file']['content_length'])) $this->content['target_file']['content_length'] = $this->content['source_file']['content_length'];
                    if (isset($this->content['source_file']['last_modified'])) $this->content['target_file']['last_modified'] = $this->content['source_file']['last_modified'];
                    if (isset($this->content['source_file']['content_type'])) $this->content['target_file']['content_type'] = $this->content['source_file']['content_type'];
                }
                else
                {
                    if ($this->content['source_file']['last_modified'] > $this->content['target_file']['last_modified'])
                    {
                        if(file_exists($this->content['target_file']['path'])) unlink($this->content['target_file']['path']);
                    }

                    if (isset($this->content['source_file']['original_file']))
                    {
                        if ($this->content['source_file']['path'] == $this->content['source_file']['original_file'])
                        {
                            unset($this->content['source_file']['original_file']);
                        }
                        else
                        {
                            if ($this->content['source_file']['last_modified'] > $this->content['target_file']['last_modified'])
                            {
                                if (!file_exists(dirname($this->content['source_file']['path']))) mkdir(dirname($this->content['source_file']['path']), 0755, true);
                                copy($this->content['source_file']['original_file'],$this->content['source_file']['path']);
                                touch($this->content['source_file']['path'], $this->content['source_file']['last_modified']);

                                $this->content['source_file']['content_length'] = filesize($this->content['source_file']['path']);
                                $this->content['source_file']['content_type'] = mime_content_type($this->content['source_file']['path']);
                            }
                        }
                    }
                    foreach ($this->request['file_extra_extension'] as $extension_index=>$extension)
                    {
                        $this->content['target_file'][$extension_index] = $this->preference->{$this->request['file_type']}[$extension_index][$extension];
                    }

                }

                if ($this->request['file_type'] == 'image')
                {
                    switch($this->request['file_extension'])
                    {
                        case 'svg':
                            $this->content['format'] = 'svg';
                            break;
                        case 'jpg':
                        case 'png':
                        case 'gif':
                        default:
                            $source_image_size = getimagesize($this->content['source_file']['path']);
                            $this->content['source_file']['width'] = $source_image_size[0];
                            $this->content['source_file']['height'] = $source_image_size[1];

                            if (!isset($this->content['target_file']['width'])) $this->content['target_file']['width'] = $this->content['source_file']['width'];
                            if (!isset($this->content['target_file']['height'])) $this->content['target_file']['height'] = $this->content['source_file']['height'] / $this->content['source_file']['width'] * $this->content['target_file']['width'];

                            // If image quality is not specified, use the fast generate setting
                            if (!isset($this->content['target_file']['quality'])) $this->content['target_file']['quality'] = $this->preference->image['quality']['spd'];
                            break;
                    }
                }
                break;
            case 'data':
            default:
                $this->content['status'] = 'OK';
                $this->content['message'] = '';
                $this->content['field'] = array();
//                $this->content['script'] = array();
//                $this->content['style'] = array();
                $this->content['style'] = ['default'=>[]];
                $this->content['script'] = ['jquery'=>['source'=>PATH_CONTENT_JS.'jquery-1.11.3.js'],'default'=>[]];
                if (isset($this->request['option']['template_name']))
                {
                    $this->content['template_name'] = $this->request['option']['template_name'];
                }
                else
                {
                    // Looking for default template
                    $template_name_part = [];
                    if (!empty($this->request['control_panel'])) $template_name_part[] = $this->request['control_panel'];
                    if (!empty($this->request['module'])) $template_name_part[] = $this->request['module'];
                    else $template_name_part[] = 'default';
                    if (!empty($this->request['method'])) $template_name_part[] = $this->request['method'];
                    if (isset($this->request['document'])) $template_name_part[] = $this->request['document'];
    //print_r($template_name_part);
                    $default_css = array();
                    $default_js = array();
                    while (!empty($template_name_part))
                    {
                        if (file_exists(PATH_CONTENT_CSS.implode('_',$template_name_part).'.css'))
                        {
                            $default_css = array_merge([implode('_',$template_name_part)=>[]],$default_css);
                        }
                        if (file_exists(PATH_CONTENT_JS.implode('_',$template_name_part).'.js'))
                        {
                            $default_js = array_merge([implode('_',$template_name_part)=>[]],$default_js);
                        }
                        if (!isset($this->content['template_name']) AND file_exists(PATH_TEMPLATE.'page_'.implode('_',$template_name_part).FILE_EXTENSION_TEMPLATE))
                        {
                            $this->content['template_name'] = 'page_'.implode('_',$template_name_part);
                        }
                        array_pop($template_name_part);
                    }

                    $this->content['style'] = array_merge($this->content['style'],$default_css);
                    $this->content['script'] = array_merge($this->content['script'],$default_js);
                    if (!isset($this->content['template_name'])) $this->content['template_name'] = 'page_default';
                }

                $this->content['field']['base'] = URI_SITE_BASE;

                switch($this->request['control_panel'])
                {
                    case 'members':
                        // Any request on members page need login account information, if not found, redirect to login page
                        if (empty($this->content['account']))
                        {
                            $this->message->error = 'Account not logged in or does not exist any more';
                            $this->result['status'] = 301;
                            $this->result['header']['Location'] =  URI_SITE_BASE.'login';
                            return false;
                        }

                        if (!empty($this->content['session']))
                        {
                            $this->result['cookie'] = ['session_id'=>['value'=>$this->content['session']['name'],'time'=>strtotime($this->content['session']['expire_time'])]];
                        }

                        $this->content['field']['robots'] = 'noindex, nofollow';
                        $this->content['field']['name'] = ucwords($this->request['control_panel']);
                        if (!empty($this->request['method']))
                        {
                            $this->content['field']['name'] .= ' - '.ucwords($this->request['method']);
                        }
                        $this->content['field']['name'] .= ' - '.$this->content['account']['name'];
                }

                switch($this->request['module'])
                {
                    case 'account':
                        switch($this->request['control_panel']) {
                            case 'members':
                                switch($this->request['method'])
                                {
                                    case 'change_password':
                                        switch($this->request['action'])
                                        {
                                            case 'update':
                                                if (!isset($this->request['option']['id']))
                                                {
                                                    $this->message->notice = 'Redirect - operating account id not set';
                                                    $this->result['status'] = 301;
                                                    $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/';
                                                    return false;
                                                }
                                                if ($this->content['account']['id'] != $this->request['option']['id'])
                                                {
                                                    $this->message->error = 'Redirect - illegal account id set, log in account is different from edit account';
                                                    $this->result['status'] = 301;
                                                    $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/';
                                                    return false;
                                                }
                                                $entity_account_obj = new entity_account($this->request['option']['id']);
                                                if (empty($entity_account_obj->id_group))
                                                {
                                                    $this->message->notice = 'Invalid account id';
                                                    $this->result['status'] = 404;
                                                    return false;
                                                }

                                                if (!is_array($this->request['option']['form_data']))
                                                {
                                                    parse_str($this->request['option']['form_data'],$this->content['form_data']);
                                                }
                                                else
                                                {
                                                    $this->content['form_data'] = $this->request['option']['form_data'];
                                                }

                                                break;
                                            default:
                                                $this->content['field']['account'] = $this->content['account'];
                                                $form_ajax_data = array(
                                                    'id'=>$this->content['account']['id']
                                                );
                                                $form_ajax_data_string = json_encode($form_ajax_data);
                                                $this->content['script']['ajax_form'] = ['content'=>'$(document).ready(function(){$(\'.ajax_form_container\').ajax_form({"form_data":'.$form_ajax_data_string.',"form_action":"save"}).trigger(\'store_form_data\');});'];
                                        }
                                        break;
                                    case 'edit':
                                        switch($this->request['action'])
                                        {
                                            case 'update':
                                                if (!isset($this->request['option']['id']))
                                                {
                                                    $this->message->notice = 'Redirect - operating account id not set';
                                                    $this->result['status'] = 301;
                                                    $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/';
                                                    return false;
                                                }
                                                if ($this->content['account']['id'] != $this->request['option']['id'])
                                                {
                                                    $this->message->error = 'Redirect - illegal account id set, log in account is different from edit account';
                                                    $this->result['status'] = 301;
                                                    $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/';
                                                    return false;
                                                }
                                                $entity_account_obj = new entity_account($this->request['option']['id']);
                                                if (empty($entity_account_obj->id_group))
                                                {
                                                    $this->message->notice = 'Invalid account id';
                                                    $this->result['status'] = 404;
                                                    return false;
                                                }

                                                if (!is_array($this->request['option']['form_data']))
                                                {
                                                    parse_str($this->request['option']['form_data'],$this->content['form_data']);
                                                }
                                                else
                                                {
                                                    $this->content['form_data'] = $this->request['option']['form_data'];
                                                }
                                                // Process image if provided
                                                if (isset($this->content['form_data']['image_image']))
                                                {
                                                    if (empty($this->content['form_data']['image_image']))
                                                    {
                                                        $image_obj = new entity_image($this->content['account']['image_id']);
                                                        $image_obj->delete();
                                                        $this->content['form_data']['image_id'] = 0;
                                                        unset($image_obj);
                                                    }
                                                    elseif (preg_match('/^data:/', $this->content['form_data']['image_image']))
                                                    {
                                                        $image_obj = new entity_image($this->content['account']['image_id']);
                                                        $image_obj->delete();
                                                        $image_obj = new entity_image();
                                                        $image_obj->set(['row'=>[['name'=>$this->content['account']['name'].' image','source_file'=>$this->content['form_data']['image_image']]]]);
                                                        $this->content['form_data']['image_id'] =  implode(',',$image_obj->id_group);
                                                        unset($image_obj);
                                                    }
                                                    unset($this->content['form_data']['image_image']);
                                                }

                                                // Process banner image if provided
                                                if (isset($this->content['form_data']['banner_image']))
                                                {
                                                    if (empty($this->content['form_data']['banner_image']))
                                                    {
                                                        $image_obj = new entity_image($this->content['account']['banner_id']);
                                                        $image_obj->delete();
                                                        $this->content['form_data']['banner_id'] = 0;
                                                        unset($image_obj);
                                                    }
                                                    elseif (preg_match('/^data:/', $this->content['form_data']['banner_image']))
                                                    {
                                                        $image_obj = new entity_image($this->content['account']['banner_id']);
                                                        $image_obj->delete();
                                                        $image_obj = new entity_image();
                                                        $image_obj->set(['row'=>[['name'=>$this->content['account']['name'].' Banner','source_file'=>$this->content['form_data']['banner_image']]]]);
                                                        $this->content['form_data']['banner_id'] =  implode(',',$image_obj->id_group);
                                                        unset($image_obj);
                                                    }
                                                    unset($this->content['form_data']['banner_image']);
                                                }

                                                // Process google place if provided
                                                if (isset($this->content['form_data']['place_id']))
                                                {
                                                    $entity_place = new entity_place();
                                                    $request = 'https://maps.googleapis.com/maps/api/place/details/json?placeid='.$this->content['form_data']['place_id'].'&key='.$this->preference->google_api_credential_server;
                                                    $response = file_get_contents($request);
                                                    if (empty($response))
                                                    {
                                                        $this->result['content']['status'] = 'REQUEST_DENIED';
                                                        $this->result['content']['message'] = 'Fail to get place info from Google, No Response';
                                                        return true;
                                                    }
                                                    $response = json_decode($response,true);
                                                    if (!is_array($response))
                                                    {
                                                        $this->result['content']['status'] = 'REQUEST_DENIED';
                                                        $this->result['content']['message'] = 'Fail to get place info from Google, Illegal Format Response';
                                                        return true;
                                                    }
                                                    if ($response['status'] != 'OK')
                                                    {
                                                        $this->result['content']['status'] = $response['status'];
                                                        $this->result['content']['message'] = 'Fail to get place info from Google. '.$response['error_message'];
                                                        return true;
                                                    }
                                                    if (empty($response['result']))
                                                    {
                                                        $this->result['content']['status'] = 'ZERO_RESULTS';
                                                        $this->result['content']['message'] = 'Fail to get place info from Google. Given Place ID returns empty result';
                                                        return true;
                                                    }
                                                    $google_place = $this->format->flatten_google_place($response['result']);
                                                    $entity_place->row[] = $google_place;

                                                    $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$google_place['location_latitude'].','.$google_place['location_longitude'].'&key='.$this->preference->google_api_credential_server;
                                                    $response = file_get_contents($request);
                                                    if (empty($response))
                                                    {
                                                        $this->result['content']['status'] = 'REQUEST_DENIED';
                                                        $this->result['content']['message'] = 'Fail to get place info from Google, No Response';
                                                        return true;
                                                    }
                                                    $response = json_decode($response,true);
                                                    if ($response['status'] != 'OK')
                                                    {
                                                        $this->result['content']['status'] = $response['status'];
                                                        $this->result['content']['message'] = 'Fail to get reverse geocoding results from Google. '.$response['error_message'];
                                                        return true;
                                                    }
                                                    if (empty($response['results']))
                                                    {
                                                        $this->result['content']['status'] = 'ZERO_RESULTS';
                                                        $this->result['content']['message'] = 'Fail to get reverse geocoding results from Google. Given Location returns empty result';
                                                        return true;
                                                    }
                                                    $region_types = ['locality','sublocality','postal_code','country','administrative_area_level_1','administrative_area_level_2'];
                                                    foreach($response['results'] as $result_row_index => $result_row)
                                                    {
                                                        $type = array_intersect($result_row['types'], $region_types);
                                                        if (!empty($type))
                                                        {
                                                            // If the result_row is a region type, store the row into tbl_entity_place and relation into tbl_rel_organization_to_place
                                                            $region_google_place = $this->format->flatten_google_place($result_row);
                                                            $place[] = $region_google_place['id'];
                                                            $entity_place->row[] = $region_google_place;
                                                        }
                                                    }
                                                    $entity_account_obj->set(['row'=>[['id'=>end($entity_account_obj->id_group),'place'=>$place]],'fields'=>['id','place']]);
                                                    $entity_place->set_place();
                                                }

                                                $entity_account_obj->update($this->content['form_data']);

                                                $entity_account_data = $entity_account_obj->get(['fields'=>array_keys($this->content['form_data'])]);
                                                if ($entity_account_data === false)
                                                {
                                                    $this->result['content']['status'] = 'SERVER_ERROR';
                                                    $this->result['content']['message'] = 'Database update request failed, try again later';
                                                    return true;
                                                }
                                                $entity_account_data = end($entity_account_data);

                                                if (isset($entity_account_data['image_id']))
                                                {
                                                    if (empty($entity_account_data['image_id']))
                                                    {
                                                        $entity_account_data['image_image'] = '';
                                                    }
                                                    else
                                                    {
                                                        $view_image_obj = new view_image($this->content['form_data']['image_id']);
                                                        $view_image_obj->fetch_value();
                                                        if (!empty($view_image_obj->row))
                                                        {
                                                            $image_data = end($view_image_obj->row);
                                                            $entity_account_data['image_image'] = $image_data['file_uri'];
                                                            unset($image_data);
                                                        }
                                                        unset($view_image_obj);
                                                    }
                                                    unset($entity_account_data['image_id']);
                                                }
                                                if (isset($entity_account_data['banner_id']))
                                                {
                                                    if (empty($entity_account_data['banner_id']))
                                                    {
                                                        $entity_account_data['banner_image'] = '';
                                                    }
                                                    else
                                                    {
                                                        $view_image_obj = new view_image($this->content['form_data']['banner_id']);
                                                        $view_image_obj->fetch_value();
                                                        if (!empty($view_image_obj->row))
                                                        {
                                                            $image_data = end($view_image_obj->row);
                                                            $entity_account_data['banner_image'] = $image_data['file_uri'];
                                                            unset($image_data);
                                                        }
                                                        unset($view_image_obj);
                                                    }
                                                    unset($entity_account_data['banner_id']);
                                                }
                                                $entity_account_obj->sync();

                                                $this->result['content']['status'] = 'OK';
                                                $this->result['content']['message'] = 'Business updated successfully';
                                                $this->result['content']['form_data'] = $entity_account_data;

                                                break;
                                            default:
                                                $this->content['field']['account'] = $this->content['account'];
                                                $image_uploader_data = array(
                                                    'width'=>200,
                                                    'height'=>200,
                                                    'allow_delete'=>true,
                                                    'shrink_large'=>true,
                                                    'default_image'=>'./image/upload_image.jpg'
                                                );
                                                $image_uploader_data_string = json_encode($image_uploader_data);
                                                $this->content['script']['logo_uploader'] = ['content'=>'$(document).ready(function(){$(\'.form_row_account_image_container\').form_image_uploader('.$image_uploader_data_string.');});'];

                                                $image_uploader_data = array(
                                                    'width'=>1200,
                                                    'height'=>375,
                                                    'allow_delete'=>true,
                                                    'default_image'=>'./image/upload_account_banner.jpg'
                                                );
                                                $image_uploader_data_string = json_encode($image_uploader_data);
                                                $this->content['script']['banner_uploader'] = ['content'=>'$(document).ready(function(){$(\'.form_row_account_banner_container\').form_image_uploader('.$image_uploader_data_string.');});'];

                                                $form_ajax_data = array(
                                                    'id'=>$this->content['account']['id']
                                                );
                                                $form_ajax_data_string = json_encode($form_ajax_data);
                                                $this->content['script']['ajax_form'] = ['content'=>'$(document).ready(function(){$(\'.ajax_form_container\').ajax_form({"form_data":'.$form_ajax_data_string.',"form_action":"save"}).trigger(\'store_form_data\');});'];

//print_r($this->content['field']['account']);exit;

                                        }
                                        break;
                                    default:

                                }
                                break;
                        }
                        break;
                    case 'article':
                        switch($this->request['method'])
                        {
                            case 'detail':
                                break;
                            case 'guide':

                                break;
                            default:
                                $ajax_loading_data = array(
                                    'data_encode'=>$this->preference->data_encode,
                                    'id_group'=>array(),
                                    'page_size'=>$this->preference->view_article_category_page_size,
                                    'page_number'=>0,
                                    'page_count'=>0
                                );
                                if (!isset($this->request['option']['id_group']))
                                {
                                    $index_article_category_obj = new index_article_category();
                                    $index_article_category_obj->filter_by_active();
                                    $this->content['field']['article_category'] = $index_article_category_obj->id_group;
                                    $ajax_loading_data['id_group'] = $index_article_category_obj->id_group;
                                }
                                else
                                {
                                    $this->content['field']['article_category'] = $this->request['option']['id_group'];
                                    $ajax_loading_data['id_group'] = $this->request['option']['id_group'];
                                }
                                if (isset($this->request['option']['page_size'])) $ajax_loading_data['page_size'] = $this->request['option']['page_size'];
                                if (isset($this->request['option']['page_number'])) $ajax_loading_data['page_number'] = $this->request['option']['page_number'];
                                $ajax_loading_data['page_count'] = ceil(count($ajax_loading_data['id_group'])/$ajax_loading_data['page_size']);

                                $ajax_loading_data_string = json_encode($ajax_loading_data);
                                if ($this->preference->data_encode == 'base64')
                                {
                                    $ajax_loading_data_string = '$.parseJSON(atob(\'' . base64_encode($ajax_loading_data_string) . '\'))';
                                }

                                $this->content['script']['article_category_ajax'] = ['content'=>'$(document).ready(function(){$(\'.ajax_loader_container\').ajax_loader('.$ajax_loading_data_string.');});'];
                                break;

                        }
                        break;
                    case 'business':
                        $view_business_detail_obj = new view_business_detail($this->request['document']);
                        if (empty($view_business_detail_obj->id_group))
                        {
                            $this->result['status'] = 404;
                            return false;
                        }
                        $business_fetched_value = $view_business_detail_obj->fetch_value();
                        if (empty($business_fetched_value))
                        {
                            // Error Handling, fetch record row failed, database data error
                            $GLOBALS['global_message']->error = __FILE__.'(line '.__LINE__.'): Fetch row failed '.implode(',',$view_business_detail_obj->id_group);
                            $this->result['status'] = 404;
                            return false;
                        }
                        $business_fetched_value = end($business_fetched_value);
                        $this->content['field']['business'] = $business_fetched_value;

                        $this->content['field']['name'] = $business_fetched_value['name'];
                        $view_category_obj = new view_category($business_fetched_value['category']);
                        $category_fetched_value = $view_category_obj->fetch_value();
                        if (!empty($category_fetched_value))
                        {
                            $category_fetched_value = end($category_fetched_value);
                            $this->content['field']['name'] .= ' - '.$category_fetched_value['name'];
                            $this->content['field']['business']['category'] = $category_fetched_value['name'];
                        }
                        $view_place_obj = new view_place();
                        $view_place_obj->get(['where'=>'id = "'.$business_fetched_value['place_id'].'"']);
                        $place_fetched_value = $view_place_obj->fetch_value();
                        if (!empty($place_fetched_value))
                        {
                            $place_fetched_value = end($place_fetched_value);
                            $this->content['field']['name'] .= ' - '.$place_fetched_value['formatted_address'];

                            $this->content['field']['business']['street_address'] = $place_fetched_value['name'];
                            $this->content['field']['business']['suburb'] = $place_fetched_value['locality'];
                            $state_name = [
                                'Australian Capital Territory'=>'ACT',
                                'New South Wales'=>'NSW',
                                'Northern Territory'=>'NT',
                                'Queensland'=>'QLD',
                                'South Australia'=>'SA',
                                'Tasmania'=>'TAS',
                                'Victoria'=>'VIC'
                            ];
                            $this->content['field']['business']['state'] = $state_name[$place_fetched_value['administrative_area_level_1']];
                            $this->content['field']['business']['postal_code'] = $place_fetched_value['postal_code'];
                            $this->content['field']['business']['latitude'] = $place_fetched_value['location_latitude'];
                            $this->content['field']['business']['longitude'] = $place_fetched_value['location_longitude'];

                            $this->content['script']['load_map'] = ['content'=>'$(\'.listing_detail_view_map .expand_trigger\').click(function(){var map_container = $(this).closest(\'.listing_detail_view_map\').find(\'.listing_detail_view_map_frame_container\');if (map_container.data(\'placeId\')){map_container.html(\'<iframe class="listing_detail_view_map_frame" src="https://www.google.com/maps/embed/v1/place?key='.$this->preference->google_api_credential_browser.'&q=place_id:'.$this->content['field']['business']['place_id'].'"></iframe>\').data(\'placeId\',\'\')}});'];
                        }
                        else
                        {
                            if (!empty($business_fetched_value['place_id']))
                            {
                                $entity_place = new entity_place();
                                $request = 'https://maps.googleapis.com/maps/api/place/details/json?placeid='.$business_fetched_value['place_id'].'&key='.$this->preference->google_api_credential_server;
                                $response = file_get_contents($request);
                                if (empty($response))
                                {
                                    $this->result['content']['status'] = 'REQUEST_DENIED';
                                    $this->result['content']['message'] = 'Fail to get place info from Google, No Response';
                                    return true;
                                }
                                $response = json_decode($response,true);
                                if (!is_array($response))
                                {
                                    $this->result['content']['status'] = 'REQUEST_DENIED';
                                    $this->result['content']['message'] = 'Fail to get place info from Google, Illegal Format Response';
                                    return true;
                                }
                                if ($response['status'] != 'OK')
                                {
                                    $this->result['content']['status'] = $response['status'];
                                    $this->result['content']['message'] = 'Fail to get place info from Google. '.$response['error_message'];
                                    file_put_contents(PATH_ASSET.'log/google_place_fail_log.txt',json_encode($response,['listing_id'=>$business_fetched_value['id'],'place_id'=>$business_fetched_value['place_id']]).PHP_EOL);
                                    return true;
                                }
                                if (empty($response['result']))
                                {
                                    $this->result['content']['status'] = 'ZERO_RESULTS';
                                    $this->result['content']['message'] = 'Fail to get place info from Google. Given Place ID returns empty result';
                                    return true;
                                }
                                $organization_google_place = $this->format->flatten_google_place($response['result']);
                                $entity_place->row[] = $organization_google_place;
                                $this->content['field']['name'] .= ' - '.$organization_google_place['formatted_address'];

                                $this->content['field']['business']['street_address'] = $organization_google_place['name'];
                                $this->content['field']['business']['suburb'] = $organization_google_place['locality'];
                                $state_name = [
                                    'Australian Capital Territory'=>'ACT',
                                    'New South Wales'=>'NSW',
                                    'Northern Territory'=>'NT',
                                    'Queensland'=>'QLD',
                                    'South Australia'=>'SA',
                                    'Tasmania'=>'TAS',
                                    'Victoria'=>'VIC'
                                ];
                                $this->content['field']['business']['state'] = $state_name[$organization_google_place['administrative_area_level_1']];
                                $this->content['field']['business']['postal_code'] = $organization_google_place['postal_code'];
                                $this->content['field']['business']['latitude'] = $organization_google_place['location_latitude'];
                                $this->content['field']['business']['longitude'] = $organization_google_place['location_longitude'];

                                $this->content['script']['load_map'] = ['content'=>'$(\'.listing_detail_view_map .expand_trigger\').click(function(){var map_container = $(this).closest(\'.listing_detail_view_map\').find(\'.listing_detail_view_map_frame_container\');if (map_container.data(\'placeId\')){map_container.html(\'<iframe class="listing_detail_view_map_frame" src="https://www.google.com/maps/embed/v1/place?key='.$this->preference->google_api_credential_browser.'&q=place_id:'.$organization_google_place['id'].'"></iframe>\').data(\'placeId\',\'\')}});'];


                                $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$organization_google_place['location_latitude'].','.$organization_google_place['location_longitude'].'&key='.$this->preference->google_api_credential_server;
                                $response = file_get_contents($request);
                                if (empty($response))
                                {
                                    $this->result['content']['status'] = 'REQUEST_DENIED';
                                    $this->result['content']['message'] = 'Fail to get place info from Google, No Response';
                                    return true;
                                }
                                $response = json_decode($response,true);
                                if ($response['status'] != 'OK')
                                {
                                    $this->result['content']['status'] = $response['status'];
                                    $this->result['content']['message'] = 'Fail to get reverse geocoding results from Google. '.$response['error_message'];
                                    return true;
                                }
                                if (empty($response['results']))
                                {
                                    $this->result['content']['status'] = 'ZERO_RESULTS';
                                    $this->result['content']['message'] = 'Fail to get reverse geocoding results from Google. Given Location returns empty result';
                                    return true;
                                }
                                $region_types = ['locality','sublocality','postal_code','country','administrative_area_level_1','administrative_area_level_2'];
                                foreach($response['results'] as $result_row_index => $result_row)
                                {
                                    $type = array_intersect($result_row['types'], $region_types);
                                    if (!empty($type))
                                    {
                                        // If the result_row is a region type, store the row into tbl_entity_place and relation into tbl_rel_organization_to_place
                                        $organization_region_google_place = $this->format->flatten_google_place($result_row);
                                        $organization_place[] = $organization_region_google_place['id'];
                                        $entity_place->row[] = $organization_region_google_place;
                                    }
                                }
                                $entity_organization_obj = new entity_organization($business_fetched_value['id']);
                                $entity_organization_obj->set(['row'=>[['id'=>$business_fetched_value['id'],'place'=>$organization_place]],'fields'=>['id','place']]);
                                $entity_place->set_place();

                            }
                        }

                        if (!empty($this->content['field']['business']['hours_work']))
                        {
                            $this->content['field']['business']['hours_work'] = json_decode($this->content['field']['business']['hours_work'],true);
                            $weekday_name_list = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                            $this->content['field']['business']['hours_work_formatted'] = [];
                            foreach ($weekday_name_list as $weekday_index=>$weekday_name)
                            {
                                $this->content['field']['business']['hours_work_formatted'][$weekday_index] = ['weekday_name'=>$weekday_name,'time_period'=>[]];;
                            }
                            if (is_array($this->content['field']['business']['hours_work']))
                            {
                                foreach ($this->content['field']['business']['hours_work'] as $weekday_index=>$weekday_hour_work)
                                {
                                    $weekday_index = $weekday_index-1;
                                    if ($weekday_index < 0) $weekday_index = 6;
                                    foreach($weekday_hour_work as $hour_work_index=>$hour_work_row)
                                    {
                                        $this->content['field']['business']['hours_work_formatted'][$weekday_index]['time_period'][] = ['opens'=>$this->format->time($hour_work_row[0]),'closes'=>$this->format->time($hour_work_row[1])];
                                    }
                                }
                            }
                        }

                        break;
                    case 'gallery':
                        switch($this->request['control_panel'])
                        {
                            case 'members':
                                switch($this->request['method'])
                                {
                                    case 'add';
                                        $this->content['field']['gallery'] = ['account_id'=>$this->content['account']['id']];

                                        if (isset($this->request['option']['organization_id']))
                                        {
                                            $entity_organization_obj = new entity_organization($this->request['option']['organization_id']);
                                            if (empty($entity_organization_obj->id_group))
                                            {
                                                $this->message->notice = 'Invalid request listing id';
                                                $this->result['status'] = 404;
                                                return false;
                                            }

                                            $entity_organization_data = $entity_organization_obj->get(['fields'=>['id','account_id','name','gallery']]);
                                            if ($entity_organization_data === false)
                                            {
                                                $this->message->error = 'Fail to get entity data';
                                                return false;
                                            }
                                            $entity_organization_data = end($entity_organization_data);

                                            if ($this->content['account']['id'] != $entity_organization_data['account_id'])
                                            {
                                                $this->message->notice = 'Unauthorised access';
                                                $this->result['status'] = 301;
                                                $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/'.$this->request['method'].'/';
                                                return false;
                                            }

                                            $this->content['field']['gallery']['name'] = $entity_organization_data['name'].' Gallery - '.date('d M, Y');
                                            $this->content['field']['gallery']['organization'] = $entity_organization_data['id'];
                                        }
                                        $entity_gallery_obj = new entity_gallery();
                                        $entity_gallery_obj->set(['row'=>[$this->content['field']['gallery']],'fields'=>array_keys($this->content['field']['gallery'])]);

                                        if (count($entity_gallery_obj->id_group) > 0)
                                        {
                                            if (isset($this->request['option']['organization_id']))
                                            {
                                                $gallery = [];
                                                if (!empty( $entity_organization_data['gallery'])) $gallery = explode(',', $entity_organization_data['gallery']);
                                                $gallery[] = end($entity_gallery_obj->id_group);

                                                $entity_organization_obj->update(['gallery'=>$gallery]);
                                                $entity_organization_obj->sync();
                                            }

                                            $this->message->notice = 'New Gallery Created, Redirect to Edit';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/'.$this->request['module'].'/edit/?id='.end($entity_gallery_obj->id_group);
                                            return true;
                                        }
                                        else
                                        {
                                            $this->message->notice = 'Creating Gallery Failed';
                                            $this->result['status'] = 403;
                                            $this->result['content'] = json_encode($this->message->display());
                                            return false;
                                        }
                                        break;
                                    case 'delete':
                                        if (!isset($this->request['option']['id']))
                                        {
                                            $this->message->notice = 'Redirect - operating gallery id not set';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/';
                                            return false;
                                        }
                                        $entity_gallery_obj = new entity_gallery($this->request['option']['id']);
                                        if (empty($entity_gallery_obj->id_group))
                                        {
                                            $this->message->notice = 'Invalid request id';
                                            $this->result['status'] = 404;
                                            return false;
                                        }
                                        $entity_gallery_data = $entity_gallery_obj->get(['relational_fields'=>['image']]);
                                        if ($entity_gallery_data === false)
                                        {
                                            $this->message->error = 'Fail to get entity data';
                                            return false;
                                        }
                                        $entity_gallery_data = end($entity_gallery_data);
                                        if ($this->content['account']['id'] != $entity_gallery_data['account_id'])
                                        {
                                            $this->message->notice = 'Unauthorised access';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/'.$this->request['method'].'/';
                                            return false;
                                        }

                                        if (!empty($entity_gallery_data['image']))
                                        {
                                            $image_obj = new entity_image($entity_gallery_data['image']);
                                            $image_obj->delete();
                                            unset($image_obj);
                                        }
                                        $entity_gallery_data = $entity_gallery_obj->get(['relational_fields'=>['organization']]);
                                        $entity_gallery_data = end($entity_gallery_data);
                                        $entity_gallery_obj->delete();

                                        if (!empty($entity_gallery_data['organization']))
                                        {
                                            $this->message->notice = 'Listing Gallery Deleted, redirect to parent listing gallery page';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/listing/gallery/?organization_id='.$entity_gallery_data['organization'];
                                            return true;
                                        }

                                        $this->message->notice = 'Gallery Deleted, redirect to members home page';
                                        $this->result['status'] = 301;
                                        $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/';
                                        return true;

                                        break;
                                    case 'edit':
                                        if (!isset($this->request['option']['id']))
                                        {
                                            $this->message->notice = 'Redirect - operating gallery id not set';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/';
                                            return false;
                                        }
                                        $entity_gallery_obj = new entity_gallery($this->request['option']['id']);
                                        if (empty($entity_gallery_obj->id_group))
                                        {
                                            $this->message->notice = 'Invalid request id';
                                            $this->result['status'] = 404;
                                            return false;
                                        }

                                        $entity_gallery_data = $entity_gallery_obj->get(['relational_fields'=>['image']]);
                                        if ($entity_gallery_data === false)
                                        {
                                            $this->message->error = 'Fail to get entity data';
                                            return false;
                                        }
                                        $entity_gallery_data = end($entity_gallery_data);
                                        if ($this->content['account']['id'] != $entity_gallery_data['account_id'])
                                        {
                                            $this->message->notice = 'Unauthorised access';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/'.$this->request['method'].'/';
                                            return false;
                                        }

                                        switch($this->request['action'])
                                        {
                                            case 'update':
                                                if (!is_array($this->request['option']['form_data']))
                                                {
                                                    parse_str($this->request['option']['form_data'],$this->content['form_data']);
                                                }
                                                else
                                                {
                                                    $this->content['form_data'] = $this->request['option']['form_data'];
                                                }
                                                if (isset($this->content['form_data']['image_row']))
                                                {
                                                    $new_image_id_group = [];

                                                    foreach ($this->content['form_data']['image_row'] as $image_row_index=>$image_row)
                                                    {
                                                        if (!empty($image_row['id']))
                                                        {
                                                            $image_obj = new entity_image($image_row['id']);
                                                            $image_obj->update($image_row);
                                                        }
                                                        else
                                                        {
                                                            $image_obj = new entity_image();
                                                            $image_obj->set(['row'=>[$image_row]]);
                                                        }
                                                        $new_image_id_group[] = end($image_obj->id_group);
                                                        unset($image_obj);
                                                    }

                                                    $current_image_id_group = [];
                                                    if (!empty($entity_gallery_data['image'])) $current_image_id_group = explode(',',$entity_gallery_data['image']);
                                                    $delete_image_id_group = array_diff($current_image_id_group,$new_image_id_group);
                                                    $sync_image_id_group = array_merge($new_image_id_group,$current_image_id_group);

                                                    $delete_image_obj = new entity_image($delete_image_id_group);
                                                    $delete_image_obj->delete();
                                                    unset($delete_image_obj);

                                                    $image_obj = new entity_image();
                                                    $image_obj->sync(['id_group'=>$sync_image_id_group]);

                                                    $this->content['form_data']['image'] = $new_image_id_group;
                                                    unset($this->content['form_data']['image_row']);
                                                }
                                                $entity_gallery_obj->update($this->content['form_data']);

                                                $entity_gallery_data = $entity_gallery_obj->get(['fields'=>['name','image']]);
                                                if ($entity_gallery_data === false)
                                                {
                                                    $this->result['content']['status'] = 'SERVER_ERROR';
                                                    $this->result['content']['message'] = 'Database update request failed, try again later';
                                                    return true;
                                                }
                                                $entity_gallery_data = end($entity_gallery_data);

                                                if (!empty($entity_gallery_data['image']))
                                                {
                                                    $view_image_obj = new view_image($entity_gallery_data['image']);
                                                    $view_image_data = $view_image_obj->fetch_value(['page_size'=>20]);
                                                    $entity_gallery_data['image_row'] = [];
                                                    foreach($view_image_data as $view_image_row)
                                                    {
                                                        $entity_gallery_data['image_row'][] = ['id'=>$view_image_row['id'],'name'=>$view_image_row['name'],'file_uri'=>$view_image_row['file_uri']];
                                                    }
                                                }

                                                $entity_gallery_obj->sync(['sync_type'=>'update_current']);

                                                $gallery_data_organization = $entity_gallery_obj->get(['relational_fields'=>['organization']]);
                                                $gallery_data_organization = end($gallery_data_organization);
                                                if (!empty($gallery_data_organization['organization']))
                                                {
                                                    $entity_organization_obj = new entity_organization($gallery_data_organization['organization']);
                                                    $entity_organization_obj->sync(['sync_type'=>'update_current']);
                                                }

                                                $this->result['content']['status'] = 'OK';
                                                $this->result['content']['message'] = 'Gallery updated successfully';
                                                $this->result['content']['form_data'] = $entity_gallery_data;
                                                break;
                                            default:
                                                $this->content['field']['gallery'] = $entity_gallery_data;
                                                $this->content['field']['back_link'] = URI_SITE_BASE.'members/';
                                                $gallery_relation_data = $entity_gallery_obj->get(['relational_fields'=>['organization']]);
                                                if (!empty($gallery_relation_data))
                                                {
                                                    $gallery_relation_data = end($gallery_relation_data);
                                                    if (!empty($gallery_relation_data['organization']))
                                                    {
                                                        $parent_entity_organization_obj = new entity_organization($gallery_relation_data['organization']);
                                                        if (!empty($parent_entity_organization_obj->id_group))
                                                        {
                                                            $this->content['field']['back_link'] = URI_SITE_BASE.'members/listing/gallery/?organization_id='.end($parent_entity_organization_obj->id_group);
                                                        }
                                                    }
                                                }
                                                $form_ajax_data = array(
                                                    'id'=>$entity_gallery_data['id'],
                                                    'name'=>$entity_gallery_data['name'],
                                                    'image_row'=>[]
                                                );
                                                if (!empty($entity_gallery_data['image']))
                                                {
                                                    $view_image_obj = new view_image($entity_gallery_data['image']);
                                                    $gallery_image_data = $view_image_obj->fetch_value();
                                                    foreach($gallery_image_data as $image_index=>$image)
                                                    {
                                                        $form_ajax_data['image_row'][] = ['id'=>$image['id'],'name'=>$image['name'],'file_uri'=>$image['file_uri'],'order'=>$image_index];
                                                    }
                                                }
                                                $form_ajax_data_string = json_encode($form_ajax_data);
                                                $this->content['script']['ajax_form'] = ['content'=>'$(document).ready(function(){$(\'.ajax_form_container\').ajax_form({"form_data":'.$form_ajax_data_string.'}).trigger(\'set_image_row\').trigger(\'store_form_data\')});'];

                                                break;
                                        }

                                        break;
                                    default:
                                        break;
                                }
                                break;
                        }
                        break;
                    case 'listing':
                        switch($this->request['control_panel'])
                        {
                            case 'members':
                                switch($this->request['method'])
                                {
                                    case 'edit':
                                        if (!isset($this->request['option']['id']))
                                        {
                                            $this->message->notice = 'Redirect - operating listing id not set';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/'.$this->request['method'].'/';
                                            return false;
                                        }
                                        $entity_organization_obj = new entity_organization($this->request['option']['id']);
                                        if (empty($entity_organization_obj->id_group))
                                        {
                                            $this->message->notice = 'Invalid request id';
                                            $this->result['status'] = 404;
                                            return false;
                                        }

                                        $entity_organization_data = $entity_organization_obj->get(['relational_fields'=>['category']]);
                                        if ($entity_organization_data === false)
                                        {
                                            $this->message->error = 'Fail to get entity data';
                                            return false;
                                        }
                                        $entity_organization_data = end($entity_organization_data);
                                        if ($this->content['account']['id'] != $entity_organization_data['account_id'])
                                        {
                                            $this->message->notice = 'Unauthorised access';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/'.$this->request['method'].'/';
                                            return false;
                                        }
                                        switch($this->request['action'])
                                        {
                                            case 'update':
                                                if (!is_array($this->request['option']['form_data']))
                                                {
                                                    parse_str($this->request['option']['form_data'],$this->content['form_data']);
                                                }
                                                else
                                                {
                                                    $this->content['form_data'] = $this->request['option']['form_data'];
                                                }
                                                // Process logo image if provided
                                                if (isset($this->content['form_data']['logo_image']))
                                                {
                                                    if (empty($this->content['form_data']['logo_image']))
                                                    {
                                                        $image_obj = new entity_image($entity_organization_data['logo_id']);
                                                        $image_obj->delete();
                                                        $this->content['form_data']['logo_id'] = 0;
                                                        unset($image_obj);
                                                    }
                                                    elseif (preg_match('/^data:/', $this->content['form_data']['logo_image']))
                                                    {
                                                        $image_obj = new entity_image($entity_organization_data['logo_id']);
                                                        $image_obj->delete();
                                                        $image_obj = new entity_image();
                                                        $image_obj->set(['row'=>[['name'=>$entity_organization_data['name'].' Logo','source_file'=>$this->content['form_data']['logo_image']]]]);
                                                        $this->content['form_data']['logo_id'] =  implode(',',$image_obj->id_group);
                                                        unset($image_obj);
                                                    }
                                                    unset($this->content['form_data']['logo_image']);
                                                }

                                                // Process banner image if provided
                                                if (isset($this->content['form_data']['banner_image']))
                                                {
                                                    if (empty($this->content['form_data']['banner_image']))
                                                    {
                                                        $image_obj = new entity_image($entity_organization_data['banner_id']);
                                                        $image_obj->delete();
                                                        $this->content['form_data']['banner_id'] = 0;
                                                        unset($image_obj);
                                                    }
                                                    elseif (preg_match('/^data:/', $this->content['form_data']['banner_image']))
                                                    {
                                                        $image_obj = new entity_image($entity_organization_data['banner_id']);
                                                        $image_obj->delete();
                                                        $image_obj = new entity_image();
                                                        $image_obj->set(['row'=>[['name'=>$entity_organization_data['name'].' Banner','source_file'=>$this->content['form_data']['banner_image']]]]);
                                                        $this->content['form_data']['banner_id'] =  implode(',',$image_obj->id_group);
                                                        unset($image_obj);
                                                    }
                                                    unset($this->content['form_data']['banner_image']);
                                                }

                                                // Process google place if provided
                                                if (isset($this->content['form_data']['place_id']))
                                                {
                                                    $entity_place = new entity_place();
                                                    $request = 'https://maps.googleapis.com/maps/api/place/details/json?placeid='.$this->content['form_data']['place_id'].'&key='.$this->preference->google_api_credential_server;
                                                    $response = file_get_contents($request);
                                                    if (empty($response))
                                                    {
                                                        $this->result['content']['status'] = 'REQUEST_DENIED';
                                                        $this->result['content']['message'] = 'Fail to get place info from Google, No Response';
                                                        return true;
                                                    }
                                                    $response = json_decode($response,true);
                                                    if (!is_array($response))
                                                    {
                                                        $this->result['content']['status'] = 'REQUEST_DENIED';
                                                        $this->result['content']['message'] = 'Fail to get place info from Google, Illegal Format Response';
                                                        return true;
                                                    }
                                                    if ($response['status'] != 'OK')
                                                    {
                                                        $this->result['content']['status'] = $response['status'];
                                                        $this->result['content']['message'] = 'Fail to get place info from Google. '.$response['error_message'];
                                                        return true;
                                                    }
                                                    if (empty($response['result']))
                                                    {
                                                        $this->result['content']['status'] = 'ZERO_RESULTS';
                                                        $this->result['content']['message'] = 'Fail to get place info from Google. Given Place ID returns empty result';
                                                        return true;
                                                    }
                                                    $organization_google_place = $this->format->flatten_google_place($response['result']);
                                                    $entity_place->row[] = $organization_google_place;

                                                    $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$organization_google_place['location_latitude'].','.$organization_google_place['location_longitude'].'&key='.$this->preference->google_api_credential_server;
                                                    $response = file_get_contents($request);
                                                    if (empty($response))
                                                    {
                                                        $this->result['content']['status'] = 'REQUEST_DENIED';
                                                        $this->result['content']['message'] = 'Fail to get place info from Google, No Response';
                                                        return true;
                                                    }
                                                    $response = json_decode($response,true);
                                                    if ($response['status'] != 'OK')
                                                    {
                                                        $this->result['content']['status'] = $response['status'];
                                                        $this->result['content']['message'] = 'Fail to get reverse geocoding results from Google. '.$response['error_message'];
                                                        return true;
                                                    }
                                                    if (empty($response['results']))
                                                    {
                                                        $this->result['content']['status'] = 'ZERO_RESULTS';
                                                        $this->result['content']['message'] = 'Fail to get reverse geocoding results from Google. Given Location returns empty result';
                                                        return true;
                                                    }
                                                    $region_types = ['locality','sublocality','postal_code','country','administrative_area_level_1','administrative_area_level_2'];
                                                    foreach($response['results'] as $result_row_index => $result_row)
                                                    {
                                                        $type = array_intersect($result_row['types'], $region_types);
                                                        if (!empty($type))
                                                        {
                                                            // If the result_row is a region type, store the row into tbl_entity_place and relation into tbl_rel_organization_to_place
                                                            $organization_region_google_place = $this->format->flatten_google_place($result_row);
                                                            $organization_place[] = $organization_region_google_place['id'];
                                                            $entity_place->row[] = $organization_region_google_place;
                                                        }
                                                    }
                                                    $entity_organization_obj->set(['row'=>[['id'=>end($entity_organization_obj->id_group),'place'=>$organization_place]],'fields'=>['id','place']]);
                                                    $entity_place->set_place();
                                                }

                                                $entity_organization_obj->update($this->content['form_data']);

                                                $entity_organization_data = $entity_organization_obj->get(['fields'=>array_keys($this->content['form_data'])]);
                                                if ($entity_organization_data === false)
                                                {
                                                    $this->result['content']['status'] = 'SERVER_ERROR';
                                                    $this->result['content']['message'] = 'Database update request failed, try again later';
                                                    return true;
                                                }
                                                $entity_organization_data = end($entity_organization_data);

                                                if (isset($entity_organization_data['logo_id']))
                                                {
                                                    if (empty($entity_organization_data['logo_id']))
                                                    {
                                                        $entity_organization_data['logo_image'] = '';
                                                    }
                                                    else
                                                    {
                                                        $view_image_obj = new view_image($this->content['form_data']['logo_id']);
                                                        $view_image_obj->fetch_value();
                                                        if (!empty($view_image_obj->row))
                                                        {
                                                            $image_data = end($view_image_obj->row);
                                                            $entity_organization_data['logo_image'] = $image_data['file_uri'];
                                                            unset($image_data);
                                                        }
                                                        unset($view_image_obj);
                                                    }
                                                    unset($entity_organization_data['logo_id']);
                                                }
                                                if (isset($entity_organization_data['banner_id']))
                                                {
                                                    if (empty($entity_organization_data['banner_id']))
                                                    {
                                                        $entity_organization_data['banner_image'] = '';
                                                    }
                                                    else
                                                    {
                                                        $view_image_obj = new view_image($this->content['form_data']['banner_id']);
                                                        $view_image_obj->fetch_value();
                                                        if (!empty($view_image_obj->row))
                                                        {
                                                            $image_data = end($view_image_obj->row);
                                                            $entity_organization_data['banner_image'] = $image_data['file_uri'];
                                                            unset($image_data);
                                                        }
                                                        unset($view_image_obj);
                                                    }
                                                    unset($entity_organization_data['banner_id']);
                                                }
                                                $entity_organization_obj->sync();

                                                $this->result['content']['status'] = 'OK';
                                                $this->result['content']['message'] = 'Business updated successfully';
                                                $this->result['content']['form_data'] = $entity_organization_data;
                                                break;
                                            default:
                                                $this->content['field']['organization'] = $entity_organization_data;
                                                $this->content['field']['organization']['hours_work'] = htmlentities($this->content['field']['organization']['hours_work']);
                                                $image_uploader_data = array(
                                                    'width'=>500,
                                                    'height'=>500,
                                                    'allow_delete'=>true,
                                                    'shrink_large'=>true,
                                                    'default_image'=>'./image/upload_logo.jpg'
                                                );
                                                $image_uploader_data_string = json_encode($image_uploader_data);
                                                $this->content['script']['logo_uploader'] = ['content'=>'$(document).ready(function(){$(\'.form_row_organization_logo_container\').form_image_uploader('.$image_uploader_data_string.');});'];

                                                $image_uploader_data = array(
                                                    'width'=>1200,
                                                    'height'=>200,
                                                    'allow_delete'=>true,
                                                    'default_image'=>'./image/upload_banner.jpg'
                                                );
                                                $image_uploader_data_string = json_encode($image_uploader_data);
                                                $this->content['script']['banner_uploader'] = ['content'=>'$(document).ready(function(){$(\'.form_row_organization_banner_container\').form_image_uploader('.$image_uploader_data_string.');});'];

                                                // Show all active category in select drop down
                                                $index_category_obj = new index_category();
                                                $index_category_obj->filter_by_active();
                                                $view_category_obj = new view_category($index_category_obj->id_group,['page_size'=>100]);
                                                $active_category = $view_category_obj->fetch_value(['table_fields'=>['id','name']]);
                                                if (empty($active_category))
                                                {
                                                    $this->message->error = 'Fail to get active categories';
                                                    return false;
                                                }
                                                $active_category_sort = [];
                                                foreach ($active_category as $row_index=>$row)
                                                {
                                                    $active_category_sort[] = $row['name'];
                                                }
                                                array_multisort($active_category_sort,$active_category);
                                                $active_category_string = json_encode(array_values($active_category));

                                                $this->content['script']['category_select'] = ['content'=>'$(document).ready(function(){$(\'#form_members_organization_category\').form_select({"select_option":'.$active_category_string.',"max_select_allowed":3});});'];

                                                $form_ajax_data = array(
                                                    'id'=>$this->request['option']['id']
                                                );
                                                $form_ajax_data_string = json_encode($form_ajax_data);
                                                $this->content['script']['ajax_form'] = ['content'=>'$(document).ready(function(){$(\'.ajax_form_container\').ajax_form({"form_data":'.$form_ajax_data_string.',"form_action":"save"}).trigger(\'store_form_data\');});'];
                                        }
                                        break;
                                    case 'gallery':
                                        if (!isset($this->request['option']['organization_id']))
                                        {
                                            $this->message->notice = 'Redirect - operating listing id not set';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/'.$this->request['method'].'/';
                                            return false;
                                        }
                                        $entity_organization_obj = new entity_organization($this->request['option']['organization_id']);
                                        if (empty($entity_organization_obj->id_group))
                                        {
                                            $this->message->notice = 'Invalid request listing id';
                                            $this->result['status'] = 404;
                                            return false;
                                        }

                                        $entity_organization_data = $entity_organization_obj->get(['fields'=>['id','account_id','name','gallery']]);
                                        if ($entity_organization_data === false)
                                        {
                                            $this->message->error = 'Fail to get entity data';
                                            return false;
                                        }
                                        $entity_organization_data = end($entity_organization_data);

                                        if ($this->content['account']['id'] != $entity_organization_data['account_id'])
                                        {
                                            $this->message->notice = 'Unauthorised access';
                                            $this->result['status'] = 301;
                                            $this->result['header']['Location'] =  URI_SITE_BASE.$this->request['control_panel'].'/'.$this->request['method'].'/';
                                            return false;
                                        }

                                        $this->content['field']['organization'] = $entity_organization_data;
                                        break;
                                    default:
                                        $ajax_loading_data = array(
                                            'data_encode'=>$this->preference->data_encode,
                                            'id_group'=>array(),
                                            'page_size'=>$this->preference->view_organization_page_size,
                                            'page_number'=>0,
                                            'page_count'=>0
                                        );
                                        if (!isset($this->request['option']['id_group']))
                                        {
                                            $index_organization_obj = new index_organization();
                                            if ($index_organization_obj->filter_by_account(['account_id'=>$this->content['account']['id']]) == false)
                                            {
                                                $this->content['field']['organization_empty'] = true;
                                            }
                                            else
                                            {
                                                $this->content['field']['organization_empty'] = false;
                                                $this->content['field']['organization'] = $index_organization_obj->id_group;
                                                $ajax_loading_data['id_group'] = $index_organization_obj->id_group;
                                            }
                                        }
                                        else
                                        {
                                            $this->content['field']['organization_empty'] = false;
                                            $this->content['field']['organization'] = $this->request['option']['id_group'];
                                            $ajax_loading_data['id_group'] = $this->request['option']['id_group'];
                                        }

                                        if ($this->content['field']['organization_empty'] == false)
                                        {
                                            if (isset($this->request['option']['page_size'])) $ajax_loading_data['page_size'] = $this->request['option']['page_size'];
                                            if (isset($this->request['option']['page_number'])) $ajax_loading_data['page_number'] = $this->request['option']['page_number'];
                                            $ajax_loading_data['page_count'] = ceil(count($ajax_loading_data['id_group'])/$ajax_loading_data['page_size']);
                                            $ajax_loading_data_string = json_encode($ajax_loading_data);

                                            if ($this->preference->data_encode == 'base64')
                                            {
                                                $ajax_loading_data_string = '$.parseJSON(atob(\'' . base64_encode($ajax_loading_data_string) . '\'))';
                                            }

                                            $this->content['script']['organization_ajax'] = ['content'=>'$(document).ready(function(){$(\'.ajax_loader_container\').ajax_loader('.$ajax_loading_data_string.');});'];
                                        }
                                }
                                break;
                            default:
                                switch($this->request['method'])
                                {
                                    case 'search':
                                        $index_organization_obj = new index_organization();
                                        if (!empty($this->request['option']['category']))
                                        {
                                            $view_category_obj = new view_category($this->request['option']['category']);
                                            if (count($view_category_obj->id_group) == 0)
                                            {
                                                $GLOBALS['global_message']->error = __FILE__.'(line '.__LINE__.'): category does not exist';
                                                $this->result['status'] = 404;
                                                return false;
                                            }
                                            $category = $view_category_obj->fetch_value();
                                            $this->content['category'] = end($category);
                                            $index_organization_obj->filter_by_category($view_category_obj->id_group);
                                        }

                                        // TODO: convert uri to place_id
                                        if (!empty($this->request['option']['state']))
                                        {

                                            if (!empty($this->request['option']['region']))
                                            {
                                                if (!empty($this->request['option']['suburb']))
                                                {
                                                    $entity_place_obj = new entity_place();
                                                    $entity_place_obj->get_from_uri($this->request['option']['suburb']);
                                                    if (count($entity_place_obj->id_group) == 0)
                                                    {
                                                        $index_organization_obj->id_group = [];
                                                    }
                                                    else
                                                    {
                                                        $index_organization_obj->filter_by_place_id($entity_place_obj->id_group);
                                                    }
                                                }
                                                $entity_place_obj = new entity_place();
                                                $entity_place_obj->get_from_uri($this->request['option']['region']);
                                                if (count($entity_place_obj->id_group) == 0)
                                                {
                                                    $index_organization_obj->id_group = [];
                                                }
                                                else
                                                {
                                                    $index_organization_obj->filter_by_place_id($entity_place_obj->id_group);
                                                }
                                            }
                                            $entity_place_obj = new entity_place();
                                            $entity_place_obj->get_from_uri($this->request['option']['state']);
                                            if (count($entity_place_obj->id_group) == 0)
                                            {
                                                $index_organization_obj->id_group = [];
                                            }
                                            else
                                            {
                                                $index_organization_obj->filter_by_place_id($entity_place_obj->id_group);
                                            }
                                        }

                                        if (!empty($this->request['option']['place_id']))
                                        {
                                            $index_organization_obj->filter_by_place_id($this->request['option']['place_id']);
                                        }

                                        if (!empty($this->request['option']['keywords']))
                                        {
                                            $index_organization_obj->filter_by_keyword($this->request['option']['keywords']);
                                        }



                                        if (count($index_organization_obj->id_group) == 0)
                                        {
                                            $GLOBALS['global_message']->notice = __FILE__.'(line '.__LINE__.'): no listing found under category '.$this->request['option']['category'];
                                            $this->result['status'] = 404;
                                            $this->result['content'] = 'No listing found under category '.$this->request['option']['category'];
                                            return false;
                                        }

                                        $this->content['field']['organization_list_title'] = $this->content['category']['name'];
                                        $this->content['field']['organization'] = $index_organization_obj->id_group;

                                        break;
                                    case '':
                                        $ajax_loading_data = array(
                                            'data_encode'=>$this->preference->data_encode,
                                            'id_group'=>array(),
                                            'page_size'=>$this->preference->view_category_page_size,
                                            'page_number'=>0,
                                            'page_count'=>0
                                        );
                                        if (!isset($this->request['option']['id_group']))
                                        {
                                            $index_category_obj = new index_category();
                                            $index_category_obj->filter_by_active();
                                            $index_category_obj->filter_by_organization_count();
                                            $this->content['field']['category'] = $index_category_obj->id_group;
                                            $ajax_loading_data['id_group'] = $index_category_obj->id_group;
                                        }
                                        else
                                        {
                                            $this->content['field']['category'] = $this->request['option']['id_group'];
                                            $ajax_loading_data['id_group'] = $this->request['option']['id_group'];
                                        }
                                        if (isset($this->request['option']['page_size'])) $ajax_loading_data['page_size'] = $this->request['option']['page_size'];
                                        if (isset($this->request['option']['page_number'])) $ajax_loading_data['page_number'] = $this->request['option']['page_number'];
                                        $ajax_loading_data['page_count'] = ceil(count($ajax_loading_data['id_group'])/$ajax_loading_data['page_size']);

                                        $ajax_loading_data_string = json_encode($ajax_loading_data);
                                        if ($this->preference->data_encode == 'base64')
                                        {
                                            $ajax_loading_data_string = '$.parseJSON(atob(\'' . base64_encode($ajax_loading_data_string) . '\'))';
                                        }

                                        $this->content['script']['category_ajax'] = ['content'=>'$(document).ready(function(){$(\'.ajax_loader_container\').ajax_loader('.$ajax_loading_data_string.');});'];
                                        break;
                                }
                        }
                        break;
                    default:
                        // Default module, front end static pages, control panel home pages...
                        switch($this->request['control_panel'])
                        {
                            case 'members':
                                // Members home page
                                if ($this->request['document'] == 'logout')
                                {
                                    // success or fail, logout page always redirect to home page after process complete
                                    $this->result['status'] = 301;
                                    $this->result['header']['Location'] =  URI_SITE_BASE;
                                    if (!isset($this->request['session_id']))
                                    {
                                        // session_id is not set, redirect to login page
                                        return true;
                                    }
                                    $this->result['cookie'] = ['session_id'=>['value'=>'','time'=>1]];

                                    $entity_account_session_obj = new entity_account_session();
                                    $get_parameter = array(
                                        'bind_param' => array(':name'=>$this->request['session_id']),
                                        'where' => array('`name` = :name')
                                    );
                                    $entity_account_session_obj->get($get_parameter);
                                    /*$method_variable = ['status' => 'OK', 'message' => '', 'account_session_id' => $this->request['session_id']];
                                    $session = $entity_account_session_obj->validate_account_session_id($method_variable);
                                    if ($session === false)
                                    {
                                        // If session_id is not valid, redirect to login page
                                        return true;
                                    }*/
                                    if (count($entity_account_session_obj->row) > 0)
                                    {
                                        // Record logout event
                                        $session_record = end($entity_account_session_obj->row);

                                        $entity_account_log_obj = new entity_account_log();
                                        $log_record = ['name'=>'Logout','account_id'=>$session_record['account_id'],'status'=>'OK','message'=>'Session close by user','content'=>$session_record['name'],'remote_ip'=>$this->request['remote_ip'],'request_uri'=>$_SERVER['REQUEST_URI']];
                                        $entity_account_obj = new entity_account($session_record['account_id']);
                                        if (count($entity_account_obj->row) > 0)
                                        {
                                            $log_record['description'] = end($entity_account_obj->row)['name'];
                                        }
                                        $entity_account_log_obj->set_log($log_record);
                                    }

                                    // If session is valid, delete the session then redirect to login
                                    $entity_account_session_obj->delete();
                                    return true;
                                }

                                $this->content['field']['page_content'] = '<a href="account" class="general_style_input_button general_style_input_button_gray">Edit Profile</a>
<a href="listing" class="general_style_input_button general_style_input_button_gray">Manage My Businesses</a>';
                                break;
                            default:
                                // Front end home page and other statistic pages
                                // If page is login, check for user login session
                                if ($this->request['document'] == 'login')
                                {
                                    if (isset($this->request['session_id']))
                                    {
                                        // session_id is set, check if it is already logged in
                                        $entity_account_session_obj = new entity_account_session();
                                        $method_variable = ['status'=>'OK','message'=>'','account_session_id'=>$this->request['session_id'],'remote_ip'=>$this->request['remote_ip']];
                                        $session = $entity_account_session_obj->validate_account_session_id($method_variable);

                                        if ($session === false)
                                        {
                                            // If session_id is not valid, unset it and continue login process
                                            $this->result['cookie'] = ['session_id'=>['value'=>'','time'=>1]];
                                        }
                                        else
                                        {
                                            $entity_account_obj = new entity_account($session['account_id']);
                                            if (empty($entity_account_obj->row))
                                            {
                                                // Error Handling, session validation succeed, session_id is valid, but cannot read corresponding account
                                                $this->message->error = 'Session Validation Succeed, but cannot find related account';
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
                                                $entity_account_obj = new entity_account();
                                                $account = $entity_account_obj->authenticate($login_param);
                                                if ($account === false)
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
                                                    $entity_account_session_obj = new entity_account_session();
                                                    $session_param = array_merge($session_param, ['account_id'=>$account['id'],'expire_time'=>gmdate('Y-m-d H:i:s',time()+$session_expire)]);
                                                    $session = $entity_account_session_obj->generate_account_session_id($session_param);

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
                                        $entity_account_log_obj = new entity_account_log();
                                        $log_record = ['name'=>'Login','remote_ip'=>$this->request['remote_ip'],'request_uri'=>$_SERVER['REQUEST_URI']];
                                        $log_record = array_merge($log_record,$this->content['post_result']);
                                        if (isset($account['id']))
                                        {
                                            $log_record['account_id'] = $account['id'];
                                            $log_record['description'] =  $account['name'];
                                        }
                                        else
                                        {
                                            $log_record['description'] =  $this->request['option']['username'];
                                        }
                                        if (isset($session['name'])) $log_record['content'] = $session['name'];
                                        $entity_account_log_obj->set_log($log_record);
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

                                    $entity_account_session_obj = new entity_account_session();
                                    $get_parameter = array(
                                        'bind_param' => array(':name'=>$this->request['session_id']),
                                        'where' => array('`name` = :name')
                                    );
                                    $entity_account_session_obj->get($get_parameter);
                                    /*$method_variable = ['status' => 'OK', 'message' => '', 'account_session_id' => $this->request['session_id']];
                                    $session = $entity_account_session_obj->validate_account_session_id($method_variable);
                                    if ($session === false)
                                    {
                                        // If session_id is not valid, redirect to login page
                                        return true;
                                    }*/
                                    if (count($entity_account_session_obj->row) > 0)
                                    {
                                        // Record logout event
                                        $session_record = end($entity_account_session_obj->row);

                                        $entity_account_log_obj = new entity_account_log();
                                        $log_record = ['name'=>'Logout','account_id'=>$session_record['account_id'],'status'=>'OK','message'=>'Session close by user','content'=>$session_record['name'],'remote_ip'=>$this->request['remote_ip'],'request_uri'=>$_SERVER['REQUEST_URI']];
                                        $entity_account_obj = new entity_account($session_record['account_id']);
                                        if (count($entity_account_obj->row) > 0)
                                        {
                                            $log_record['description'] = end($entity_account_obj->row)['name'];
                                        }
                                        $entity_account_log_obj->set_log($log_record);
                                    }

                                    // If session is valid, delete the session then redirect to login
                                    $entity_account_session_obj->delete();
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
                                        $this->result['status'] = 404;
                                        //$this->result['status'] = 301;
                                        //$this->result['header']['Location'] =  URI_SITE_BASE.'login';
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
                                    $this->content['field']['banner_title'] = $this->content['field']['page_title'];
                                    $this->content['field']['banner_description'] = $this->content['field']['description'];

                                    if ($this->request['document'] == 'login' OR $this->request['document'] == 'signup' )
                                    {
                                        $this->content['field']['complementary'] = base64_encode(json_encode(['remote_addr'=>get_remote_ip(), 'http_user_agent'=>$_SERVER['HTTP_USER_AGENT'], 'submission_id'=>sha1(openssl_random_pseudo_bytes(5))]));
                                    }
                                    if ($this->request['document'] == '')
                                    {
                                        $index_organization_obj = new index_organization();
                                        $listing_id_group = $index_organization_obj->filter_by_featured();
                                        $this->content['field']['featured_business'] = $listing_id_group;

                                        $index_category_obj = new index_category();
                                        $index_category_obj->filter_by_active();
                                        $category_listing_count = $index_category_obj->filter_by_organization_count();
                                        $category_id_group = $index_category_obj->id_group;
                                        if (!empty($category_id_group))
                                        {
                                            if (count($category_id_group) > 16)
                                            {
                                                // If there are more than 16 categories in system, get the top 16
                                                $category_id_group = array_slice($category_id_group, 0, 16);
                                            }
                                            foreach ($category_id_group as $category_id_index=>$category_id)
                                            {
                                                // Remove the categories that have less than 100 listings
                                                if ($category_listing_count[$category_id] < 100)
                                                {
                                                    unset($category_id_group[$category_id_index]);
                                                }
                                            }
                                            // Random the order of categories selected
                                            shuffle($category_id_group);
                                            if (count($category_id_group) >= 8)
                                            {
                                                // If there are more than 8 categories selected, display 8
                                                $category_id_group = array_slice($category_id_group, 0, 8);
                                            }
                                            else
                                            {
                                                // If there are less than 8 categories, display 4 (or less)
                                                $category_id_group = array_slice($category_id_group, 0, 4);
                                            }
                                            $this->content['field']['popular_category'] = $category_id_group;
                                        }
                                    }
                                }

                        }
                }

            //print_r($this->content['script']);exit();


                //$this->result['content'] = render_html($this->content['field'],$this->content['template_name']);


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
                        file_put_contents($this->content['target_file']['path'],minify_content(file_get_contents($this->content['target_file']['path']),$this->request['file_extension']));
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

                switch ($this->request['file_extension'])
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

                // If the required file is not the default file, generate the required file
                if ($this->content['source_file']['path'] != $this->content['target_file']['path'])
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

                    $image_quality = $this->content['target_file']['quality'];
                    ob_start();
                    switch($this->content['source_file']['content_type'])
                    {
                        case 'image/png':
                            imagesavealpha($target_image, true);
                            imagepng($target_image, NULL, $image_quality['image/png'][0], $image_quality['image/png'][1]);
                            $this->content['target_file']['content_type'] = 'image/png';
                            break;
                        case 'image/gif':
                        case 'image/jpg':
                        case 'image/jpeg':
                        default:
                            imagejpeg($target_image, NULL, $image_quality['image/jpeg']);
                            $this->content['target_file']['content_type'] = 'image/jpeg';
                    }
                    $this->result['content'] = ob_get_contents();
                    ob_get_clean();
                    if (!file_exists(dirname($this->content['target_file']['path']))) mkdir(dirname($this->content['target_file']['path']), 0755, true);
                    file_put_contents($this->content['target_file']['path'],$this->result['content']);
                    //$this->content['target_file']['content_length'] = filesize($this->content['target_file']['path']);
                    $this->content['target_file']['content_length'] = strlen($this->result['content']);
                    touch($this->content['target_file']['path'],$this->content['source_file']['last_modified']);
                    $this->content['target_file']['last_modified'] = $this->content['source_file']['last_modified'];

                    // Default image create process finish here, unset default file gd object
                    unset($target_image);
                }
                if (empty($this->content['target_file']['last_modified']) AND file_exists($this->content['target_file']['path'])) $this->content['target_file']['last_modified'] = filemtime($this->content['target_file']['path']);

                $this->result['header']['Last-Modified'] = gmdate('D, d M Y H:i:s',$this->content['target_file']['last_modified']).' GMT';
                $this->result['header']['Content-Length'] = $this->content['target_file']['content_length'];
                $this->result['header']['Content-Type'] = $this->content['target_file']['content_type'];

                if (!isset($this->result['content'])) $this->result['file_path'] = $this->content['target_file']['path'];
                break;
            case 'svg':
                $this->content['target_file']['last_modified'] = 'image/svg+xml';

                // TODO: svg is basically xml file, file_get_content -> minify as html -> file_put_content?
                if ($this->content['source_file']['path'] != $this->content['target_file']['path'])
                {
                    copy($this->content['source_file']['path'],$this->content['target_file']['path']);
                    touch($this->content['target_file']['path'],$this->content['source_file']['last_modified']);
                    $this->content['target_file']['last_modified'] = $this->content['source_file']['last_modified'];
                    $this->content['target_file']['last_modified'] = $this->content['source_file']['content_length'];
                }

                $this->result['header']['Last-Modified'] = gmdate('D, d M Y H:i:s',$this->content['target_file']['last_modified']).' GMT';
                $this->result['header']['Content-Length'] = $this->content['target_file']['content_length'];
                $this->result['header']['Content-Type'] = $this->content['target_file']['content_type'];

                if (!isset($this->result['content'])) $this->result['file_path'] = $this->content['target_file']['path'];
                break;
            case 'json':
                if (!isset($GLOBALS['global_field'])) $GLOBALS['global_field'] = array();
                switch($this->request['control_panel'])
                {
                    case 'members':
                        switch($this->request['module'])
                        {
                            case 'listing':
                                switch($this->request['method'])
                                {
                                    case 'edit':
                                        break;
                                    case '':
                                        $this->result['content']['html'] = render_html(['_value'=>$this->content['field'],'_parameter'=>['template'=>'[[organization:template_name=`view_members_organization_summary`]]']]);
                                        break;
                                }
                                break;
                        }
                        break;
                    default:
                        switch($this->request['module'])
                        {
                            case 'listing':
                                switch($this->request['method'])
                                {
                                    case '':
                                        $this->result['content']['html'] = render_html(['_value'=>$this->content['field'],'_parameter'=>['template'=>'[[category]]']]);
                                        break;
                                }
                                break;
                        }

                }
                if (isset($GLOBALS['global_field']['style']))
                {
                    $combined_content = '';
                    foreach($GLOBALS['global_field']['style'] as $index=>$item)
                    {
                        $combined_content .= $item['content'];
                    }
                    $this->result['content']['style'] = $combined_content;
                    unset($combined_content);
                }
                if (isset($GLOBALS['global_field']['script']))
                {
                    $combined_content = '';
                    foreach($GLOBALS['global_field']['script'] as $index=>$item)
                    {
                        $combined_content .= $item['content'];
                    }
                    $this->result['content']['script'] = $combined_content;
                    unset($combined_content);
                }
                $this->result['content'] = json_encode($this->result['content']);
                //$this->result['content'] = json_encode($this->content['api_result']);
                $this->result['header']['Last-Modified'] = gmdate('D, d M Y H:i:s').' GMT';
                $this->result['header']['Content-Length'] = strlen($this->result['content']);
                $this->result['header']['Content-Type'] = 'application/json';
                break;
            case 'xml':
                $this->result['content'] = render_xml($this->content['api_result'])->asXML();
                $this->result['header']['Last-Modified'] = gmdate('D, d M Y H:i:s').' GMT';
                $this->result['header']['Content-Length'] = strlen($this->result['content']);
                $this->result['header']['Content-Type'] = 'text/xml';
                break;
            case 'html':
                if (!isset($this->content['field'])) $this->content['field'] = array();
                if (!isset($this->content['template_name'])) $this->content['template_name'] = '';
                $GLOBALS['global_field'] = array();
                $this->result['content'] = render_html(['_value'=>$this->content['field'],'_parameter'=>['template_name'=>$this->content['template_name']]]);
//echo 'test point 3'."\n";
//print_r($GLOBALS['global_field']);
                if (isset($GLOBALS['global_field']['style'])) $this->content['style'] = array_merge($this->content['style'],$GLOBALS['global_field']['style']);
                if (isset($GLOBALS['global_field']['script'])) $this->content['script'] = array_merge($this->content['script'],$GLOBALS['global_field']['script']);
                $this->result['content'] = preg_replace('/\[\[\+/','[[*',$this->result['content']);
                if (!empty($this->content['style']))
                {
//print_r($this->content['style']);
                    $this->content['field']['style'] = ['_value'=>[],'_parameter'=>['template_name'=>'chunk_html_tag']];
                    $file_extension = '.css';
                    if ($this->preference->minify_css)
                    {
                        $file_extension = '.min'.$file_extension;
                    }
                    foreach($this->content['style'] as $name=>$option)
                    {
                        $attribute = [
                            'type'=>'text/css'
                        ];
                        if (!isset($option['content']))
                        {
                            $tag = [
                                'name'=>'link',
                                'non_void_element'=>false
                            ];
                            $attribute['rel'] = 'stylesheet';
                            if (!empty($option['name'])) $attribute['href'] = URI_CSS.$option['name'].$file_extension;
                            else $attribute['href'] = URI_CSS.$name.$file_extension;
                        }
                        else
                        {
                            $tag = [
                                'name'=>'style',
                                'non_void_element'=>true,
                                'content'=>$option['content']
                            ];
                        }

                        if (isset($option['attribute']))  $attribute = array_merge($attribute,$option['attribute']);
                        $attribute_set = [];
                        foreach($attribute as $attribute_name=>$attribute_value)
                        {
                            $attribute_set[] = ['name'=>$attribute_name,'value'=>$attribute_value];
                        }
                        $tag['attribute'] = $attribute_set;
                        unset($attribute_set);

                        $this->content['field']['style']['_value'][] = $tag;
                        unset($tag);
                    }
                }
                if (!empty($this->content['script']))
                {
                    $this->content['field']['script'] = ['_value'=>[],'_parameter'=>['template_name'=>'chunk_html_tag']];
                    $file_extension = '.js';
                    if ($this->preference->minify_js)
                    {
                        $file_extension = '.min'.$file_extension;
                    }
                    foreach($this->content['script'] as $name=>$option)
                    {
                        $tag = [
                            'name'=>'script',
                            'non_void_element'=>true
                        ];
                        if (isset($option['content']))  $tag['content'] = $option['content'];
                        $attribute = [
                            'type'=>'text/javascript'
                        ];
                        if (!isset($option['content']))
                        {
                            $attribute['src'] = URI_JS.$name.$file_extension;
                        }

                        if (isset($option['source']))
                        {
                            $attribute['src'] .= '?source='.urlencode($option['source']);
                        }

                        if (isset($option['attribute']))  $attribute = array_merge($attribute,$option['attribute']);
                        $attribute_set = [];
                        foreach($attribute as $attribute_name=>$attribute_value)
                        {
                            $attribute_set[] = ['name'=>$attribute_name,'value'=>$attribute_value];
                        }
                        $tag['attribute'] = $attribute_set;
                        unset($attribute_set);

                        $this->content['field']['script']['_value'][] = $tag;
                        unset($tag);
                    }
                }
                $this->result['content'] = render_html(['_value'=>$this->content['field'],'_parameter'=>['template'=>$this->result['content']]]);

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
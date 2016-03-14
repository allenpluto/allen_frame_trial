<?php
// Include Class Object
// Name: content
// Description: web page content functions

// Render template, create html page view...

class content {
    protected $template = null;
    protected $content = array();
    protected $cache = 0;
    protected $cached_page_file = '';
    protected $uri_parameter = array();

    function __construct($parameter)
    {
        $format = format::get_obj();
        $this->uri_decoder($parameter);
        $template = PREFIX_TEMPLATE_PAGE.$this->uri_parameter['namespace'];

        // Looking for cached page
        $this->cached_page_file =  PATH_CACHE_PAGE . $this->uri_parameter['namespace'] . '/';
        if (isset($parameter['instance']))
        {
            if (!empty($parameter['instance']))
            {
                $this->cached_page_file .= $this->uri_parameter['instance'] . '/';
            }
        }
        if (isset($parameter['extra_parameter']))
        {
            if (!empty($parameter['extra_parameter']))
            {
                $this->cached_page_file .= $parameter['extra_parameter'] . '/';
            }
        }
        $nocache = false;
        if (isset($parameter['nocache']))
        {
            if ($parameter['nocache'] == true) $nocache = true;
        }
        if (file_exists($this->cached_page_file.'index.html') AND !$nocache)
        {
            $cached_page_content = file_get_contents($this->cached_page_file.'index.html');
            preg_match_all('/\<\!\-\-(\{.*\})\-\-\>/', $cached_page_content, $matches, PREG_OFFSET_CAPTURE);
            $cached_page_parameter = array();
            foreach($matches[1] as $key=>$value)
            {
                $json_decode_result = json_decode($value[0],true);
                if (is_array($json_decode_result)) $cached_page_parameter = array_merge($cached_page_parameter, $json_decode_result);
            }

            if (isset($cached_page_parameter['Expire']) AND strtotime($cached_page_parameter['Expire']) >= strtotime('now'))
            {
                preg_replace('/\<\!\-\-\{.*\}\-\-\>/', '', $cached_page_content);
                $this->content = $cached_page_content;
                return true;
            }
            else
            {
                unlink($this->cached_page_file.'index.html');
            }
        }

        switch ($this->uri_parameter['namespace'])
        {
            case 'asset':
                switch ($this->uri_parameter['instance'])
                {
                    case 'image':
                        if (!file_exists(PATH_IMAGE . $this->uri_parameter['image_size'] . '/' . $this->uri_parameter['image_file']))
                        {
                            if (empty($this->uri_parameter['image_size']))
                            {
                                header('Location: '.URI_IMAGE.'default/'.$this->uri_parameter['image_file']);
                                exit();
                            }
                            if ($this->uri_parameter['image_size'] == 'default')
                            {
                                $source_image_path = URI_IMAGE_EXTERNAL.$this->uri_parameter['image_file'];
                                unset($target_width);
                            }
                            else
                            {
                                $default_image_path = PATH_IMAGE . 'default/' . $this->uri_parameter['image_file'];
                                if (file_exists($default_image_path))
                                {
                                    $source_image_path = $default_image_path;
                                    $default_image_exists = true;
                                }
                                else
                                {
                                    $source_image_path = URI_IMAGE_EXTERNAL.$this->uri_parameter['image_file'];
                                    $default_image_exists = false;
                                }
                                $target_width = $this->uri_parameter['image_width'];
                            }
                            if (isset($this->uri_parameter['image_source'])) $source_image_path = $this->uri_parameter['image_source'];

                            $source_image_size = getimagesize($source_image_path);

                            if (!empty($source_image_size[0]))
                            {
                                switch($source_image_size['mime'])
                                {
                                    case 'image/png':
                                        $source_image = imagecreatefrompng($source_image_path);
                                        break;
                                    case 'image/gif':
                                        $source_image = imagecreatefromgif($source_image_path);
                                        break;
                                    case 'image/jpg':
                                    case 'image/jpeg':
                                        $source_image = imagecreatefromjpeg($source_image_path);
                                        break;
                                    default:
                                        $source_image = imagecreatefromstring($source_image_path);
                                }

                                if ($source_image === FALSE)
                                {
                                    header('Location: '.URI_SITE_BASE.'/content/image/img_listing_default_280_280.jpg');
                                    exit();
                                }


                                if (!isset($target_width)) $target_width = min($source_image_size[0],  $GLOBALS['global_preference']->image_size_xxl);
                                $target_height = $source_image_size[1] / $source_image_size[0] *  $target_width;
                                $target_image = imagecreatetruecolor($target_width, $target_height);

                                imagecopyresized($target_image, $source_image,0,0,0,0,$target_width, $target_height,$source_image_size[0], $source_image_size[1]);

                                if (!$default_image_exists)
                                {
                                    $default_image_width = min($source_image_size[0],  $GLOBALS['global_preference']->image_size_xxl);
                                    $default_image_height = $source_image_size[1] / $source_image_size[0] *  $default_image_width;
                                    $default_image = imagecreatetruecolor($default_image_width, $default_image_height);

                                    imagecopyresized($default_image, $source_image,0,0,0,0,$default_image_width, $default_image_height,$source_image_size[0], $source_image_size[1]);
                                }

                                if (!file_exists(PATH_IMAGE. $this->uri_parameter['image_size'] . '/'))
                                {
                                    mkdir(PATH_IMAGE. $this->uri_parameter['image_size'] . '/', 0755, true);
                                }
                                if (!file_exists(PATH_IMAGE. 'default/'))
                                {
                                    mkdir(PATH_IMAGE. 'default/', 0755, true);
                                }

                                if (!isset($source_image_size['mime'])) $source_image_size['mime'] = 'image/jpeg';
                                $target_image_path = PATH_IMAGE. $this->uri_parameter['image_size'] . '/' . $this->uri_parameter['image_file'];
                                switch($source_image_size['mime'])
                                {
                                    case 'image/png':
                                        if (!$default_image_exists)
                                        {
                                            imagepng($default_image, $default_image_path, 0, PNG_NO_FILTER);
                                        }
                                        imageinterlace($target_image,true);
                                        imagepng($target_image, $target_image_path, 9, PNG_ALL_FILTERS);
                                        break;
                                    case 'image/gif':
                                        if (!$default_image_exists)
                                        {
                                            imagegif($default_image, $default_image_path);
                                        }
                                        imageinterlace($target_image,true);
                                        imagegif($target_image, $target_image_path);
                                        break;
                                    case 'image/jpg':
                                    case 'image/jpeg':
                                    default:
                                        if (!$default_image_exists)
                                        {
                                            imagejpeg($default_image, $default_image_path, 100);
                                        }
                                        imageinterlace($target_image,true);
                                        imagejpeg($target_image, $target_image_path, 75);
                                }
                                imagedestroy($source_image);
                                imagedestroy($target_image);
                                if (!$default_image_exists)
                                {
                                    imagedestroy($default_image);
                                }
                                header('Content-type: '.$source_image_size['mime']);
                                header('Content-Length: '.filesize($target_image_path));
                                readfile($target_image_path);
                            }
                        }
                        break;
                }
                break;
            case 'business':
                $this->cache = 3;
//echo '<pre>';
                $view_business_detail_obj = new view_business_detail($this->uri_parameter['instance']);
//print_r($view_business_detail_obj);
//exit();
                $view_business_detail_value = $view_business_detail_obj->fetch_value();

                $render_parameter = array(
                    'build_from_content'=>array(
                        array(
                            'name'=>$view_business_detail_value[0]['name'],
                            'description'=>$view_business_detail_value[0]['description'],
                            'body'=>$view_business_detail_obj
                        )
                    )
                );
                $render_parameter = array_merge($this->uri_parameter, $render_parameter);
                $view_web_page_obj = new view_web_page(null, $render_parameter);
                $this->content = $view_web_page_obj->render();
                break;
            case 'listing':
                $page_parameter = $format->pagination_param($parameter);
                if ($page_parameter === false) $page_parameter = array();
                switch ($this->uri_parameter['instance'])
                {
                    case '':
                        $this->cache = 1;
                        $index_category_obj = new index_category();
                        $index_category_obj->filter_by_active();
                        $index_category_obj->filter_by_listing_count();
                        $view_category_obj = new view_category($index_category_obj->id_group);
                        $inpage_script = '$(document).ready(function(){$(\'.ajax_loader_container\').ajax_loader($.parseJSON(atob(\''.base64_encode(json_encode(array('object_type'=>'business_category','data_encode_type'=>'base64','id_group'=>$view_category_obj->id_group,'page_size'=>$view_category_obj->parameter['page_size'],'page_number'=>$view_category_obj->parameter['page_number'],'page_count'=>$view_category_obj->parameter['page_count']))).'\')));});';
                        $view_web_page_element_obj_body = new view_web_page_element(null, array(
                            'template'=>'element_body_section',
                            'build_from_content'=>array(
                                array(
                                    'id'=>'category_container',
                                    'class_extra'=>' category_block_wrapper',
                                    'title'=>'<h1>Popular Categories</h1>',
                                    'content'=>'<div class="column_container ajax_loader_container">'.$view_category_obj->render().'<div class="clear"></div></div>'
                                ),
                            )
                        ));
                        $render_parameter = array(
                            'build_from_content'=>array(
                                array(
                                    'name'=>'Top4 Businesses Australian Local Listings',
                                    'description'=>'Find restaurants, hotels, plumbers, accountants and all kinds of local businesses with The New Australian Social Media Top4 Business and Brand Directory.',
                                    'meta_keywords'=>'business category, local services, social directory, top4',
                                    'inpage_script'=>$inpage_script,
                                    'body'=>$view_web_page_element_obj_body
                                )
                            )
                        );
                        $render_parameter = array_merge($this->uri_parameter, $render_parameter);
                        $view_web_page_obj = new view_web_page(null,$render_parameter);
                        $this->content = $view_web_page_obj->render();

                        break;
                    case 'ajax-load':
                        if (!isset($_POST['object_type']))
                        {
                            $_POST['object_type'] = 'business';
                        }
                        if (!isset($_POST['id_group']))
                        {
                            $this->content = '';
                            break;
                        }
                        switch($_POST['object_type'])
                        {
                            case 'business_category':
                                $view_category_obj = new view_category($_POST['id_group'],array('page_size'=>$_POST['page_size'],'page_number'=>$_POST['page_number']));
                                $this->content = $view_category_obj->render();
                                break;
                            case 'business':
                                $view_business_summary_obj = new view_business_summary($_POST['id_group'],array('page_size'=>$_POST['page_size'],'page_number'=>$_POST['page_number']));
                                $this->content = $view_business_summary_obj->render();
                                break;
                            default:
                                // unknown object type
                                $GLOBALS['global_message']->warning = __FILE__.'(line '.__LINE__.'): '.$this->uri_parameter['namespace'].' '.$this->uri_parameter['instance'].' unknown type';

                        }

                        if ($GLOBALS['global_preference']->minify_html)
                        {
                            $this->content = $format->minify_html($this->content);
                        }

                        if (isset($_POST['data_encode_type']))
                        {
                            switch ($_POST['data_encode_type'])
                            {
                                case 'none':
                                    break;
                                case 'base64':
                                default:
                                    // unknown encode type default to base64
                                    $this->content = base64_encode($this->content);
                                    break;

                            }
                        }
                        $GLOBALS['global_preference']->minify_html = 0;

                        break;
                    case 'find':
                        $this->cache = 1;
                        $index_organization_obj = new index_organization();
                        if (empty($this->uri_parameter['category']))
                        {
                            header('Location: /'.URI_SITE_PATH.$this->uri_parameter['namespace'].'/');
                            exit();
                        }

                        $view_category_obj = new view_category($this->uri_parameter['category']);
                        if ($view_category_obj->id_group == 0)
                        {
                            header('Location: /'.URI_SITE_PATH.$this->uri_parameter['namespace'].'/');
                            exit();
                        }
                        $index_organization_obj->filter_by_category($view_category_obj->id_group);

                        if (!empty($this->uri_parameter['state']))
                        {
                            $index_location_obj = new index_location();
                            $index_location_obj->filter_by_location_parameter($this->uri_parameter);

                            $index_organization_obj->filter_by_location($index_location_obj->id_group);
                        }
                        $view_business_summary_obj = new view_business_summary($index_organization_obj->id_group, $page_parameter);
                        if (count($view_business_summary_obj->id_group) > 0)
                        {
                            $content = '<div id="search_result_listing_block_wrapper" class="listing_block_wrapper block_wrapper ajax_loader_container">'.$view_business_summary_obj->render().'<div class="clear"></div></div>';
                            $inpage_script = '$(document).ready(function(){$(\'#search_result_listing_block_wrapper\').ajax_loader($.parseJSON(atob(\''.base64_encode(json_encode(array('data_encode_type'=>'base64','id_group'=>$view_business_summary_obj->id_group,'page_size'=>$view_business_summary_obj->parameter['page_size'],'page_number'=>$view_business_summary_obj->parameter['page_number'],'page_count'=>$view_business_summary_obj->parameter['page_count']))).'\')));});';
                        }
                        else
                        {
                            $content = '<div class="section_container container article_container"><div class="section_title"><h2>Here\'s how we can help you find what you\'re looking for:</h2></div><div class="section_content"><ul><li>Check the spelling and try again.</li><li>Try a different suburb or region.</li><li>Try a more general search.</li></ul></div></div>';
                            $inpage_script = '';
                        }

                        $category_row = $view_category_obj->fetch_value();
                        $long_title = 'Top 4 '.$category_row[0]['page_title'];

                        $view_web_page_element_obj_body = new view_web_page_element(null, array(
                            'template'=>'element_body_section',
                            'build_from_content'=>array(
                                array(
                                    'id'=>'listing_search_result_container',
                                    'title'=>'<h1>'.$long_title.'</h1>',
                                    'content'=>$content
                                )
                            )
                        ));

                        $render_parameter = array(
                            'template'=>PREFIX_TEMPLATE_PAGE.'default',
                            'build_from_content'=>array(
                                array(
                                    'name'=>$long_title,
                                    'description'=>$long_title,
                                    'inpage_script'=>$inpage_script,
                                    'body'=>$view_web_page_element_obj_body
                                )
                            )
                        );
                        $render_parameter = array_merge($this->uri_parameter, $render_parameter);
                        $view_web_page_obj = new view_web_page(null,$render_parameter);
                        $this->content = $view_web_page_obj->render();

                        break;
                    case 'search':
                        $index_organization_obj = new index_organization();
                        if (!isset($parameter['extra_parameter']))
                        {
                            $GLOBALS['global_message']->warning = __FILE__.'(line '.__LINE__.'): '.get_class($this).' URL pointing to search without search terms';
                            header('Location: /'.URI_SITE_PATH.$this->uri_parameter['namespace'].'/');
                            exit();
                        }
                        $ulr_part = $format->split_uri($parameter['extra_parameter']);

                        if (empty($ulr_part[0]) OR $ulr_part[0] == 'empty')
                        {
                            $GLOBALS['global_message']->error = __FILE__.'(line '.__LINE__.'): '.get_class($this).' illegal search term';
                            header('Location: /'.URI_SITE_PATH.$this->uri_parameter['namespace'].'/');
                            exit();
                        }
                        $what =  trim(html_entity_decode(strtolower($ulr_part[0])));
                        $score = $index_organization_obj->filter_by_keyword($ulr_part[0]);

                        $where = '';
                        if (isset($ulr_part[2]))
                        {
                            $where = trim(html_entity_decode(strtolower($ulr_part[2])));
                            if (strtolower($ulr_part[1]) == 'where' AND $where != 'empty')
                            {
                                $score = $index_organization_obj->filter_by_location($ulr_part[2],array('preset_score'=>$score));
                            }

                        }
                        $view_business_summary_obj = new view_business_summary($index_organization_obj->id_group, $page_parameter);
                        if (count($view_business_summary_obj->id_group) > 0)
                        {
                            $content = '<div id="search_result_listing_block_wrapper" class="listing_block_wrapper block_wrapper ajax_loader_container">'.$view_business_summary_obj->render().'<div class="clear"></div></div>';
                            //$inpage_script = '$(document).ready(function(){$(\'.listing_block_wrapper\').data('.json_encode(array('id_group'=>$view_business_summary_obj->id_group,'page_size'=>$view_business_summary_obj->parameter['page_size'],'page_number'=>$view_business_summary_obj->parameter['page_number'],'page_count'=>$view_business_summary_obj->parameter['page_count'])).');});';
                            $inpage_script = '$(document).ready(function(){$(\'#search_result_listing_block_wrapper\').ajax_loader($.parseJSON(atob(\''.base64_encode(json_encode(array('data_encode_type'=>'base64','id_group'=>$view_business_summary_obj->id_group,'page_size'=>$view_business_summary_obj->parameter['page_size'],'page_number'=>$view_business_summary_obj->parameter['page_number'],'page_count'=>$view_business_summary_obj->parameter['page_count']))).'\')));});';
                        }
                        else
                        {
                            $content = '<div class="section_container container article_container"><div class="section_title"><h2>Here\'s how we can help you find what you\'re looking for:</h2></div><div class="section_content"><ul><li>Check the spelling and try again.</li><li>Try a different suburb or region.</li><li>Try a more general search.</li></ul></div></div>';
                            $inpage_script = '';
                        }


                        $long_title = 'Search '.($what?html_entity_decode($what):'Business').' in '.($where?$where:'Australia');

                        $view_web_page_element_obj_body = new view_web_page_element(null, array(
                            'template'=>'element_body_section',
                            'build_from_content'=>array(
                                array(
                                    'id'=>'listing_search_result_container',
                                    'title'=>'<h1>'.$long_title.'</h1>',
                                    'content'=>$content
                                )
                            )
                        ));

                        $render_parameter = array(
                            'template'=>PREFIX_TEMPLATE_PAGE.'default',
                            'build_from_content'=>array(
                                array(
                                    'name'=>$long_title,
                                    'description'=>$long_title,
                                    'inpage_script'=>$inpage_script,
                                    'body'=>$view_web_page_element_obj_body
                                )
                            )
                        );
                        $render_parameter = array_merge($this->uri_parameter, $render_parameter);
                        $view_web_page_obj = new view_web_page(null,$render_parameter);
                        $this->content = $view_web_page_obj->render();

                        break;
                    default:
                        header('Location: /'.URI_SITE_PATH.$this->uri_parameter['namespace'].'/');
                }
                break;
            default:
                switch ($this->uri_parameter['instance'])
                {
                    case 'home':
                        $this->cache = 1;
                        $index_organization_obj = new index_organization();
                        $view_business_summary_obj = new view_business_summary($index_organization_obj->filter_by_featured(),array('page_size'=>4,'order'=>'RAND()'));
                        $inpage_script = '$(document).ready(function(){$(\'.ajax_loader_container\').ajax_loader($.parseJSON(atob(\''.base64_encode(json_encode(array('data_encode_type'=>'base64','id_group'=>$view_business_summary_obj->id_group,'page_size'=>$view_business_summary_obj->parameter['page_size'],'page_number'=>$view_business_summary_obj->parameter['page_number'],'page_count'=>$view_business_summary_obj->parameter['page_count']))).'\')));});';

                        $view_web_page_element_obj_body = new view_web_page_element(null, array(
                            'template'=>'element_body_section',
                            'build_from_content'=>array(
                                array(
                                    'id'=>'home_featured_listing_container',
                                    'title'=>'<h2>Featured</h2>',
                                    'content'=>'<div class="listing_block_wrapper block_wrapper ajax_loader_container">'.$view_business_summary_obj->render().'<div class="clear"></div></div>'
                                ),
                                /*array(
                                    'id'=>'home_listing_category_container',
                                    'title'=>'Category',
                                    'content'=>'Some Category here...'
                                )*/
                            )
                        ));

                        $render_parameter = array(
                            'build_from_content'=>array(
                                array(
                                    'name'=>'Top4 - The New Australian Social Media Business and Brand Directory',
                                    'description'=>'Top4 is the new Australian Social Media Business and Brand Directory designed to help Australians find and connect with any business, product, brand, job or person nearest their location.',
                                    'meta_keywords'=>'social directory, australian business brand',
                                    'inpage_script'=>$inpage_script,
                                    'body'=>$view_web_page_element_obj_body
                                )
                            )
                        );
                        $render_parameter = array_merge($this->uri_parameter, $render_parameter);
                        $view_web_page_obj = new view_web_page(null, $render_parameter);
                        //$doc = new DOMDocument();
                        //$doc->loadHTML($view_web_page_obj->render());
                        $this->content = $view_web_page_obj->render();
                        break;
                    case '404':
                        header("HTTP/1.0 404 Not Found");
                        $this->content = '404 Not Found';
                        break;
                    default:
                        $this->cache = 10;
                        $view_web_page_obj = new view_web_page($this->uri_parameter['instance'],$this->uri_parameter);
                        if (count($view_web_page_obj->id_group) == 0)
                        {
                            header('Location: ./404');
                        }
                        $this->content = $view_web_page_obj->render();
                }
        }
    }

    private function uri_decoder($value)
    {
        $format = format::get_obj();
        if (empty($value))
        {
            return false;
        }
        if (is_array($value))
        {
            if (!empty($value['value']))
            {
                extract($value);
            }
        }
        if (!isset($parameter)) $parameter = array();

        $result = array();
        if (is_string($value))
        {
            $uri_part = $format->split_uri($value);
            $result['namespace'] = isset($uri_part[0])?$uri_part[0]:'default';
            $result['instance'] = isset($uri_part[1])?$uri_part[1]:'home';
            $sub_uri = array_slice($uri_part, 2);
        }
        else
        {
            $result['namespace'] = isset($value['namespace'])?$value['namespace']:'default';
            $result['instance'] = isset($value['instance'])?$value['instance']:'home';
            $sub_uri = isset($value['extra_parameter'])?$format->split_uri($value['extra_parameter']):array();
        }

        switch($result['namespace'])
        {
            case 'asset':
                switch($result['instance'])
                {
                    case 'image':
                        if (count($sub_uri) > 1)
                        {
                            $result['image_file'] = end($sub_uri);
                            $result['image_size'] = $sub_uri[0];
                            if (!empty($result['image_size']) AND $result['image_size'] != 'default')
                            {
                                if ($GLOBALS['global_preference']->{'image_size_'.$result['image_size']})
                                {
                                    $result['image_width'] = $GLOBALS['global_preference']->{'image_size_'.$result['image_size']};
                                }
                                else
                                {
                                    $result['image_size'] = 'default';
                                    $GLOBALS['global_message']->notice = __FILE__.'(line '.__LINE__.'): invalid file size option "'.$value['size'].'" for file "'.$result['image_file'].'"';
                                }
                            }
                            if (isset($value['image_source']))
                            {
                                $result['image_source'] = $value['image_source'];
                            }
                        }
                        else return false;
                        break;
                }
                break;
            case 'listing':
                switch($result['instance'])
                {
                    case 'find':
                        if (!empty($sub_uri[0])) $result['category'] = $sub_uri[0];
                        else return false;
                        if (!empty($sub_uri[1])) $result['state'] = $sub_uri[1];
                        if (!empty($sub_uri[2])) $result['region'] = str_replace('-',' ',$sub_uri[2]);
                        if (!empty($sub_uri[3])) $result['suburb'] = str_replace('-',' ',$sub_uri[3]);
                        break;
                    case 'search':
                        if (!empty($sub_uri[0])) $result['what'] = $sub_uri[0];
                        else return false;
                        if (isset($sub_uri[1]) AND $sub_uri[1] == 'where' AND !empty($sub_uri[2])) $result['where'] = $sub_uri[2];
                        break;
                    default:
                }
                break;
            default:
        }

        $this->uri_parameter = $result;

    }

    function render($parameter = array())
    {
        header('Content-Type: text/html; charset=utf-8');

        $format = format::get_obj();
        if ($GLOBALS['global_preference']->minify_html)
        {
            $this->content = $format->minify_html($this->content);
        }

        if ($this->cache > 0)
        {
            $expire_time = strtotime('+'.$this->cache.' day');
            $cache_parameter = array('Expire'=>date('d M, Y', $expire_time));
            if (!file_exists($this->cached_page_file))
            {
                mkdir($this->cached_page_file, 0755, true);
            }
            file_put_contents($this->cached_page_file.'index.html',$this->content.'<!--'.json_encode($cache_parameter).'-->');
        }

        print_r($this->content);
        return true;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26/09/2016
 * Time: 2:24 PM
 */
if (!isset($start_time)) $start_time = microtime(1);

function render_xml($field = array(), &$xml = NULL, $parent_node_name = '')
{
    if (!isset($xml)) $xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
    foreach ($field as $field_name=>$field_value)
    {
        if (!empty($parent_node_name)) $field_name = $parent_node_name;
        if (is_array($field_value))
        {
            // For sequential array
            if (array_keys($field_value) === range(0, count($field_value) - 1))
            {
                $parent_node = $xml->addChild($field_name.'s');
                render_xml($field_value,$parent_node,$field_name);
            }
            else
            {
                $parent_node = $xml->addChild($field_name);
                render_xml($field_value,$parent_node);
            }
        }
        else
        {
            $xml->addChild($field_name,htmlspecialchars($field_value));
        }
    }
    return $xml;
}

function render_html($field = array(), $template_name = '', $container_name = '', $separator = NULL)
{
    //if (empty($template_name)) return '';
$GLOBALS['time_stack']['analyse template '.$template_name] = microtime(1) - $GLOBALS['start_time'];

    $field_parameter = array();
    if (isset($field['_parameter']))
    {
        $field_parameter = $field['_parameter'];
        unset($field['_parameter']);
    }
    if (isset($field_parameter['condition']) AND empty($field_parameter['condition'])) return '';
    if (!empty($template_name))
    {
        $field_parameter['template_name'] = $template_name;
    }
    if (isset($field_parameter['template_name']))
    {
        if (file_exists(PATH_TEMPLATE.$field_parameter['template_name'].FILE_EXTENSION_TEMPLATE))
        {
            $field_parameter['template'] = file_get_contents(PATH_TEMPLATE.$field_parameter['template_name'].FILE_EXTENSION_TEMPLATE);
        }
        else
        {
            $GLOBALS['global_message']->notice = 'rendering error: template ['.PATH_TEMPLATE.$field_parameter['template_name'].FILE_EXTENSION_TEMPLATE.'] file does not exist';
            $field_parameter['template'] = '[[*_placeholder]]';
        }
    }
    if (!empty($container_name))
    {
        $field_parameter['container_name'] = $container_name;
    }
    if (isset($field_parameter['container_name']))
    {
        if (file_exists(PATH_TEMPLATE.$field_parameter['container_name'].FILE_EXTENSION_TEMPLATE)) $field_parameter['container'] = file_get_contents(PATH_TEMPLATE.$field_parameter['container_name'].FILE_EXTENSION_TEMPLATE);
        {
            $GLOBALS['global_message']->warning = 'rendering error: container ['.PATH_TEMPLATE.$field_parameter['container_name'].FILE_EXTENSION_TEMPLATE.'] file does not exist';
            $field_parameter['container'] = '[[*_placeholder]]';
        }
    }
    if (isset($separator))
    {
        $field_parameter['separator'] = $separator;
    }
    else
    {
        $field_parameter['separator'] = '';
    }
    if (isset($field['_value']))
    {
        $field = $field['_value'];
    }
    if (!is_array($field) OR !isset($field[0]))
    {
        $field = [$field];
    }

    $template_match = array();
    $rendered_content = array();
    foreach($field as $field_row_index=>&$field_row)
    {
        if (!is_array($field_row))
        {
            $field_row = ['_placeholder'=>$field_row];
        }
        else
        {
            if (isset($field_row['_parameter']))
            {
                $field_row_parameter = array_merge($field_parameter,$field_row['_parameter']);
                unset($field_row['_parameter']);
            }
            else
            {
                $field_row_parameter = $field_parameter;
            }

            if (empty($field_row_parameter['template'])) continue;

            if (!isset($field_row_parameter['template_name']))
            {
                $template_counter = 0;
                while (isset($template_match['template_'.$template_counter]))
                {
                    if ($template_match['template_'.$template_counter]['template'] == $field_row_parameter['template'])
                    {
                        $field_row_parameter['template_name'] = 'template_'.$template_counter;
                        break;
                    }
                    $template_counter++;
                }
                if (!isset($field_row_parameter['template_name']))
                {
                    $field_row_parameter['template_name'] = 'template_'.$template_counter;
                    $template_match['template_'.$template_counter]['template'] = $field_row_parameter['template'];
                }
            }
        }

        if (isset($field_parameter['parent_row']))
        {
            $field_row = array_merge($field_parameter['parent_row'],$field_row);
        }

        $match_result = array();
        if (!isset($template_match[$field_row_parameter['template_name']]['match_result']))
        {
            $template_match[$field_row_parameter['template_name']]['match_result'] = array();
            $template_match[$field_row_parameter['template_name']]['template_translated'] = $field_row_parameter['template'];
            // If there are multi layer template variables, chunks..., set match_result array from inner ones to outer ones, due to regular express limitation
            while(preg_match_all('/\[\[((\W*)([^\[]+?))\]\]/', $template_match[$field_row_parameter['template_name']]['template_translated'], $matches))
            {
                $template_translate = array();
                foreach($matches[3] as $match_key=>$match_value)
                {
                    $current_item = '{{'.sha1($matches[1][$match_key]).'}}';
                    if (!isset($match_result[$current_item]))
                    {
                        $match_value_array = explode(':',$match_value);
                        $match_result[$current_item] = array('type'=>$matches[2][$match_key],'name'=>$match_value_array[0],'raw_code'=>$matches[0][$match_key]);
                        unset($match_value_array);
                        $template_translate[$matches[0][$match_key]] = $current_item;

                        if (preg_match_all('/:(\w+?)=`(.*?)`/', $match_value, $match_items))
                        {
                            foreach ($match_items[2] as $match_item_index=>$match_item_value)
                            {
                                $match_result[$current_item][$match_items[1][$match_item_index]] = $match_item_value;
                            }
                        }
                    }
                }

                // If higher layer template variable decoded, put them on top of match_result array
                $template_match[$field_row_parameter['template_name']]['match_result'] = array_merge($match_result,$template_match[$field_row_parameter['template_name']]['match_result']);
                if ($field_row_parameter['template_name'] == 'element_body_section')
                {
                    print_r($match_result);
                }

                unset($matches);
                unset($match_result);

                // For template variables already decoded, change their code from [[template_variable]] to {{template_variable}}, then loop, so it can decode outer layer tv without conflict
                $template_match[$field_row_parameter['template_name']]['template_translated'] = strtr($template_match[$field_row_parameter['template_name']]['template_translated'],$template_translate);
                //$field_row_parameter['template'] = preg_replace('/\[\[([^\[]*?)\]\]/','{{$1}}',$field_row_parameter['template']);
            }
            if ($field_row_parameter['template_name'] == 'element_body_section')
            {
                print_r($template_match[$field_row_parameter['template_name']]['template_translated']);
                print_r($template_match[$field_row_parameter['template_name']]['match_result']);
            }
        }
        else
        {
            $field_row_parameter['template'] = $template_match[$field_row_parameter['template_name']]['template_translated'];
        }

        $field_row_rendered_content = $template_match[$field_row_parameter['template_name']]['template_translated'];
        $translate_array = array();
        $match_result = $template_match[$field_row_parameter['template_name']]['match_result'];

        foreach($match_result as $match_result_key=>&$match_result_value)
        {
            //$match_result_value = array_merge($match_result_value,$field_row_parameter);
            if (isset($match_result_value['condition']))
            {
                if (empty($match_result_value['condition']))
                {
                    $match_result_value['value'] = '';
                }
                else
                {
                    if (isset($field_row[$match_result_value['condition']]) AND empty($field_row[$match_result_value['condition']]))
                    {
                        $match_result_value['value'] = '';
                    }
                }
            }
            switch($match_result_value['type'])
            {
                case '*':
                    // Field value, directly set value from given field
                    if (isset($field_row[$match_result_value['name']]))
                    {
                        if (empty($field_row[$match_result_value['name']]))
                        {
                            $match_result_value['value'] = '';
                        }
                        else
                        {
                            if (is_array($field_row[$match_result_value['name']]))
                            {
                                // If field value is still an array, it needs to be rendered again
                                if (!isset($match_result_value['template_name'])) $match_result_value['template_name'] = $field_row_parameter['template_name'].'_'.$match_result_value['name'];
                                $field_row_variable_template = '';

                                if (file_exists(PATH_TEMPLATE.$match_result_value['template_name'].FILE_EXTENSION_TEMPLATE))
                                {
                                    $field_row_variable_template = $match_result_value['template_name'];
                                }
                                if (!isset($field_row[$match_result_value['name']]['_parameter']))
                                {
                                    $field_row[$match_result_value['name']]['_parameter'] = array();
                                }
                                if (!isset($field_row[$match_result_value['name']]['_parameter']['parent_row']))
                                {
                                    $field_row[$match_result_value['name']]['_parameter']['parent_row'] = array();
                                }
                                if (isset($field_row[$match_result_value['name']]['_parameter']['template_name']))
                                {
                                    $field_row_variable_template = $field_row[$match_result_value['name']]['_parameter']['template_name'];
                                }
                                $field_row[$match_result_value['name']]['_parameter']['parent_row'] = array_merge($field_row,$field_row[$match_result_value['name']]['_parameter']['parent_row']);
                                unset($field_row[$match_result_value['name']]['_parameter']['parent_row'][$match_result_value['name']]);
                                $match_result_value['value'] = render_html($field_row[$match_result_value['name']],$field_row_variable_template);
                            }
                            else
                            {
                                $match_result_value['value'] = $field_row[$match_result_value['name']];
                            }
                        }
                    }
                    else $match_result_value['value'] = '';
                    break;
                case '$':
                    // Chunk, load sub-template
                    if (!isset($match_result_value['condition'])) $match_result_value['condition'] = true;
                    else $match_result_value['condition'] = $field_row[$match_result_value['condition']];
                    if (!isset($match_result_value['alternative_chunk'])) $match_result_value['alternative_chunk'] = '';
                    if (isset($match_result_value['field'])) $field_row = array_merge($field_row, json_decode($match_result_value['field'],true));
                    if ($match_result_value['condition']) $match_result_value['value'] = render_html($field_row,$match_result_value['name']);
                    else $match_result_value['value'] = render_html($field_row,$match_result_value['alternative_chunk']);
                    break;
                case '':
                    // Object, fetch value and render for each row
                    if (empty($field_row[$match_result_value['name']]))
                    {
                        $match_result_value['value'] = '';
                        break;
                    }

                    if (!isset($match_result_value['object']))
                    {
                        if (class_exists('view_'.$match_result_value['name']))
                        {
                            $match_result_value['object'] = 'view_'.$match_result_value['name'];
                        }
                        else
                        {
                            $match_result_value['object'] = 'entity_'.$match_result_value['name'];
                        }
                    }
                    if (!isset($match_result_value['template_name']))
                    {
                        $match_result_value['template_name'] = $field_row_parameter['template_name'].'_'.$match_result_value['name'];
                        if (!file_exists(PATH_TEMPLATE.$match_result_value['template_name'].FILE_EXTENSION_TEMPLATE)) $match_result_value['template_name'] = $match_result_value['object'];
                    }

                    if (!file_exists(PATH_TEMPLATE.$match_result_value['template_name'].FILE_EXTENSION_TEMPLATE))
                    {
                        $match_result_value['value'] = '';
                        break;
                    }
                    $GLOBALS['time_stack']['analyse parameter for object '.$match_result_value['object']] = microtime(1) - $GLOBALS['start_time'];
                    try
                    {
                        $object = new $match_result_value['object']($field[$match_result_value['name']]);
                        $GLOBALS['time_stack']['create object 1 '.$match_result_value['object']] = microtime(1) - $GLOBALS['start_time'];
                    }
                    catch (Exception $e)
                    {
                        // Error Handling, error rendering sub object during render_html
                        $GLOBALS['global_message']->error = 'error rendering sub object during render_html'.$e->getMessage();
                        $match_result_value['value'] = $e->getMessage();
                        break;
                    }
                    $GLOBALS['time_stack']['create object '.$match_result_value['object']] = microtime(1) - $GLOBALS['start_time'];

                    $result = $object->fetch_value();
                    $GLOBALS['time_stack']['fetch value '.$match_result_value['object']] = microtime(1) - $GLOBALS['start_time'];
//print_r($object);
                    unset($object);
                    $sub_field = array_values($result);
                    unset($result);
                    $sub_field_parent_row = $field_row;
                    if (isset($match_result_value['field'])) $sub_field_parent_row = array_merge($sub_field_parent_row, json_decode($match_result_value['field'],true));
                    $match_result_value['value'] = render_html(['_value'=>$sub_field,'_parameter'=>['parent_row'=>$sub_field_parent_row]],$match_result_value['template_name']);
                    unset($sub_field_parent_row);

                    break;
                case '-':
                    $match_result_value['value'] = '';
                    break;
                case '+':
                    // do not replace, keep for further operation, such as insert style or script
                    $match_result_value['value'] = '';
                    if (isset($field_row[$match_result_value['name']]))
                    {
                        foreach($field_row[$match_result_value['name']] as $resource_index=>&$resource)
                        {
                            $resource_obj =  new content($resource);
                            //print_r($resource_obj);
                            $match_result_value['value'] .= $resource_obj->get_result();
                        }
                    }
                    else $match_result_value['value'] = '';
                    break;
                default:
                    $match_result_value['value'] = '';
            }
            if (isset($match_result_value['value']))
            {
                $translate_array[$match_result_key] = $match_result_value['value'];
            }
            $GLOBALS['time_stack']['render variable '.$match_result_key] = microtime(1) - $GLOBALS['start_time'];
        }

        $counter = 0;
        while(preg_match('/{{(.*?)}}/',$field_row_rendered_content))
        {
            $field_row_rendered_content = strtr($field_row_rendered_content,$translate_array);
            $counter++;
            if ($counter > 4) break;
        }
if ($field_row_parameter['template_name'] == 'element_body_section')
{
    print_r($field_row_rendered_content);
    print_r($translate_array);
    exit;
}
        //$field_row_rendered_content = preg_replace('/{{(.*?)}}/','[[$1]]',$field_row_rendered_content);

        // self loop, if page still have untranslated template variables (place holder type excepted, [[+example]], as they are not suppose to be translated at all), use same field and template to render again
        if (preg_match('/\[\[[^\+](.*?)\]\]/', $field_row_rendered_content))
        {
            $field_row_rendered_content = render_html(['_value'=>$field_row,'_parameter'=>['template'=>$field_row_rendered_content]]);
        }

        if (!empty($field_row_parameter['container']) AND !empty($field_row_rendered_content))
        {
            $field_row_rendered_content = str_replace('[[*_placeholder]]',$field_row_rendered_content,$field_row_parameter['row_container']);
        }

        $rendered_content[] = $field_row_rendered_content;
    }

    $field_rendered_content = implode($field_parameter['separator'],$rendered_content);

    if (!empty($field_parameter['container']) AND !empty($field_rendered_content))
    {
        $field_rendered_content = str_replace('[[*_placeholder]]',$field_rendered_content,$field_parameter['container']);
    }

    return $field_rendered_content;

}

function minify_content($value, $type='html')
{
    if (empty($value))
    {
        return '';
    }

    switch($type)
    {
        case 'css':
            // Minify CSS

            // Remove comments
            $search = array(
                '/\/\*(.*?)\*\//s'                  // remove css comments
            );
            $replace = array(
                ''
            );
            $value = preg_replace($search, $replace, $value);

            // Preserve Quoted Content
            $counter = 0;
            $quoted_content = array();
            while(preg_match('/"((?:[^"]|\\")*?)(?<!\\\)"/',$value,$matches,PREG_OFFSET_CAPTURE))
            {
                $quoted_content['[[*quoted_content_'.$counter.']]'] = $matches[0][0];
                $value = substr_replace($value,'[[*quoted_content_'.$counter.']]',$matches[0][1],strlen($matches[0][0]));
                $counter++;
            }
            while(preg_match('/\'((?:[^\']|\\\')*?)(?<!\\\)\'/',$value,$matches,PREG_OFFSET_CAPTURE))
            {
                $quoted_content['[[*quoted_content_'.$counter.']]'] = $matches[0][0];
                $value = substr_replace($value,'[[*quoted_content_'.$counter.']]',$matches[0][1],strlen($matches[0][0]));
                $counter++;
            }
            unset($counter);

            // Minify Content
            $search = array(
                '/([,:;\{\}])[^\S]+/',             // strip whitespaces after , : ; { }
                '/[^\S]+([,:;\{\}])/',             // strip whitespaces before , : ; { }
                '/(\s)+/'                            // shorten multiple whitespace sequences
            );
            $replace = array(
                '\\1',
                '\\1',
                '\\1'
            );
            $value = preg_replace($search, $replace, $value);

            // Restore Quoted Content
            for ($i=0;$i<2;$i++)  $value = strtr($value,$quoted_content);

            return $value;
        case 'html':
            // Minify HTML

            // Remove comments
            $search = array(
                '/<\!--(?!\[if)(.*?)-->/s'       // remove html comments, except IE comments
            );
            $replace = array(
                ''
            );
            $value = preg_replace($search, $replace, $value);

            // Preserve inline script and style
            $counter = 0;
            $quoted_content = array();
            while(preg_match('/\<script(.*?)\<\/script\>/',$value,$matches,PREG_OFFSET_CAPTURE))
            {
                $quoted_content['[[*quoted_content_'.$counter.']]'] = $matches[0][0];
                $value = substr_replace($value,'[[*quoted_content_'.$counter.']]',$matches[0][1],strlen($matches[0][0]));
                $counter++;
            }
            while(preg_match('/\<style(.*?)\<\/style\>/',$value,$matches,PREG_OFFSET_CAPTURE))
            {
                $quoted_content['[[*quoted_content_'.$counter.']]'] = $matches[0][0];
                $value = substr_replace($value,'[[*quoted_content_'.$counter.']]',$matches[0][1],strlen($matches[0][0]));
                $counter++;
            }
            unset($counter);

            // Minify Content
            $search = array(
                '/\>[^\S ]+/',                      // strip whitespaces after tags, except space
                '/[^\S ]+\</',                      // strip whitespaces before tags, except space
                '/(\s)+/'                            // shorten multiple whitespace sequences
            );
            $replace = array(
                '>',
                '<',
                '\\1'
            );
            $value = preg_replace($search, $replace, $value);

            // Restore Quoted Content
            for ($i=0;$i<2;$i++)  $value = strtr($value,$quoted_content);

            return $value;
        case 'js':
            // Minify JS

            // Remove comments
            $search = array(
                '/\/\*(.*?)\*\//s',                       // remove js comments with /* */
                '/\/\/(.*?)[\n\r]/s'                     // remove js comments with //
            );
            $replace = array(
                '',
                ''
            );
            $value = preg_replace($search, $replace, $value);

            // Preserve Quoted Content
            $counter = 0;
            $quoted_content = array();
            while(preg_match('/"(?:[^"]|\\")*?(?<!\\\)"/',$value,$matches,PREG_OFFSET_CAPTURE))
            {
                $quoted_content['[[*quoted_content_'.$counter.']]'] = $matches[0][0];
                $value = substr_replace($value,'[[*quoted_content_'.$counter.']]',$matches[0][1],strlen($matches[0][0]));
                $counter++;
            }
            while(preg_match('/\'(?:[^\']|\\\')*?(?<!\\\)\'/',$value,$matches,PREG_OFFSET_CAPTURE))
            {
                $quoted_content['[[*quoted_content_'.$counter.']]'] = $matches[0][0];
                $value = substr_replace($value,'[[*quoted_content_'.$counter.']]',$matches[0][1],strlen($matches[0][0]));
                $counter++;
            }
            while(preg_match('/(?<!\/)\/(?:[^\/\n\r]|\\\\\/)+?(?<!\\\)\//',$value,$matches,PREG_OFFSET_CAPTURE))
            {
                $quoted_content['[[*quoted_content_'.$counter.']]'] = $matches[0][0];
                $value = substr_replace($value,'[[*quoted_content_'.$counter.']]',$matches[0][1],strlen($matches[0][0]));
                $counter++;
            }
            unset($counter);
            unset($matches);

            // Minify Content
            $search = array(
                '/([\<\>\=\+\-,:;\(\)\{\}])[^\S]+/',        // strip whitespaces after , : ; { }
                '/[^\S]+([\<\>\=\+\-,:;\(\)\{\}])/',        // strip whitespaces before , : ; { }
                '/^(\s)+/'                                 // strip whitespaces in the start of the file
            );
            $replace = array(
                '\\1',
                '\\1',
                ''
            );
            $value = preg_replace($search, $replace, $value);

            // Restore Quoted Content
            for ($i=0;$i<3;$i++)  $value = strtr($value,$quoted_content);

            return $value;
        default:
            // Error Handling, minify unknown type
            $GLOBALS['global_message']->error = 'minify_content - unrecognized minify type '.$type;
            return false;
    }
}

function password_hashing($value)
{
    if (is_array($value))
    {
        extract($value);
    }
    if (isset($password))
    {
        $value = $password;
    }
    if (isset($name) AND !isset($salt))
    {
        $salt = substr(hash('sha256',$name),-5);
    }
    if (empty($value))
    {
        return False;
    }
    if (!isset($salt))
    {
        $salt = '';
    }
    $result = hash('sha256',hash('crc32b',$value.$salt));
    return $result;
}

function get_remote_ip()
{
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']))
    {
        // For Cloud Flare forwarded request, get the original remote ip address
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
    {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'];
}
<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15/05/2018
 * Time: 5:06 PM
 */
define('PATH_SITE_BASE', '/wamp/www/allen_frame_trial/');
include('../../system/config/config.php');

$entity_schema_type = new entity(Null,[
    'table'=>'tbl_entity_schema_type'
]);
$row = $entity_schema_type->get([
    'where'=>['tbl_entity_schema_type.page_scanned = 0'],
    'limit'=>1
]);
if (!empty($_POST))
{
    if (isset($_POST['name']))
    {
        $_POST['name'] = trim(preg_replace('/([A-Z])/',' $1',$_POST['name']));
        $_POST['friendly_url'] = $GLOBALS['format']->file_name($_POST['name']);
    }
    if (isset($_POST['property_row']))
    {
        $entity_schema_property = new entity(Null,[
            'table'=>'tbl_entity_schema_property',
            'relational_fields'=>[
                'expected_type'=>[
                    'table'=>'tbl_rel_expected_type_to_schema_property',
                    'source_id_field'=>'schema_property_id',
                    'target_id_field'=>'schema_type_id'
                ]
            ]
        ]);
//        print_r($entity_schema_property);
//        print_r($_POST['property_row']);
        foreach($_POST['property_row'] as $property_index=>&$property_row)
        {
            if (isset($property_row['expected_type_row']))
            {
                $entity_schema_type_children = new entity(Null,['table'=>'tbl_entity_schema_type','table_fields'=>['alternate_name','page_url']]);
                $entity_schema_type_children->set(['row'=>$property_row['expected_type_row']]);
                // alternate_name is unique index, but not primary key, duplicate rows will not return id, need to do get()
                unset($entity_schema_type_children);
                $entity_schema_type_children = new entity(Null,['table'=>'tbl_entity_schema_type']);
                $alternate_name_array = [];
                foreach ($property_row['expected_type_row'] as $type_index=>$type_row)
                {
                    $alternate_name_array[] = '"'.$type_row['alternate_name'].'"';
                }
                $entity_schema_type_children->get(['where'=>'`alternate_name` IN ('.implode(',',$alternate_name_array).')']);
                unset($property_row['expected_type_row']);
                $property_row['expected_type'] = array_values($entity_schema_type_children->id_group);
                unset($entity_schema_type_children);
            }
            if (isset($property_row['alternate_name']))
            {
                $property_row['alternate_name'] = trim($property_row['alternate_name']);
                $property_row['name'] = ucwords(trim(preg_replace('/([A-Z])/',' $1',$property_row['alternate_name'])));
                $property_row['friendly_url'] = $GLOBALS['format']->file_name($property_row['name']);

                $entity_schema_property_single = new entity(Null,[
                    'table'=>'tbl_entity_schema_property',
                    'relational_fields'=>[
                        'expected_type'=>[
                            'table'=>'tbl_rel_expected_type_to_schema_property',
                            'source_id_field'=>'schema_property_id',
                            'target_id_field'=>'schema_type_id'
                        ]
                    ]
                ]);
                $entity_schema_property_single->get(['where'=>'alternate_name = "'.$property_row['alternate_name'].'"']);
                if (count($entity_schema_property_single->id_group) > 0)
                {
                    $entity_schema_property_single->update($property_row);
                    unset($_POST['property_row'][$property_index]);
                }
            }
        }

        $entity_schema_property->set(['row'=>$_POST['property_row']]);
        // alternate_name is unique index, but not primary key, duplicate rows will not return id, need to do get()
        unset($entity_schema_property);
        $entity_schema_property = new entity(Null,[
            'table'=>'tbl_entity_schema_property'
        ]);
        $alternate_name_array = [];
        foreach ($_POST['property_row'] as $type_index=>$type_row)
        {
            $alternate_name_array[] = '"'.$type_row['alternate_name'].'"';
        }
        $entity_schema_property->get(['where'=>'alternate_name IN ('.implode(',',$alternate_name_array).')']);
        unset($_POST['property_row']);
        $_POST['schema_property'] = array_values($entity_schema_property->id_group);
    }
    if (isset($_POST['children_row']))
    {
        $entity_schema_type_children = new entity(Null,[
            'table'=>'tbl_entity_schema_type',
            'table_fields'=>['alternate_name','page_url']
        ]);
        $entity_schema_type_children->set(['row'=>$_POST['children_row']]);
        // alternate_name is unique index, but not primary key, duplicate rows will not return id, need to do get()
        unset($entity_schema_type_children);
        $entity_schema_type_children = new entity(Null,[
            'table'=>'tbl_entity_schema_type'
        ]);
        $alternate_name_array = [];
        foreach ($_POST['children_row'] as $type_index=>$type_row)
        {
            $alternate_name_array[] = '"'.$type_row['alternate_name'].'"';
        }
        $entity_schema_type_children->get(['where'=>'`alternate_name` IN ('.implode(',',$alternate_name_array).')']);
        unset($_POST['children_row']);
        $_POST['schema_type'] = array_values($entity_schema_type_children->id_group);
        unset($entity_schema_type_children);
    }
    $entity_schema_type_current = new entity($_POST['id'],[
        'table'=>'tbl_entity_schema_type',
        'relational_fields'=>[
                'schema_type'=>[
                    'table'=>'tbl_rel_parent_to_schema_type',
                    'source_id_field'=>'parent_id',
                    'target_id_field'=>'schema_type_id'
                ],
            'schema_property'=>[
                'table'=>'tbl_rel_schema_proerpty_to_schema_type',
                'source_id_field'=>'schema_type_id'
            ]
        ]
    ]);
    $entity_schema_type_current->update($_POST);

    if (!empty($row)){
        $current_schema_type = end($row);
        echo json_encode($current_schema_type);
    }

    exit;
}

if (!empty($row)):
$current_schema_type = end($row);
    if (!empty($current_schema_type['page_url'])):
//echo file_get_contents($current_schema_type['page_url']);
?>
<div id="content_container">
<?=file_get_contents($current_schema_type['page_url']);?>
</div>
<script src="../../content/js/jquery-1.11.3.js"></script>
<script>
function scan_data()
{
    console.log('scan data');
    var post_data = {
        'id':<?=$current_schema_type['id']?>,
        'name':$('h1.page-title').text(),
        'description':$('div[property="rdfs:comment"]').text(),
        'canonical_url':$('.canonicalUrl a').text(),
        'page_scanned':1
    };
    var property_row = [];
    $('tbody.supertype').each(function(){
        if ($(this).prev().text().trim() == 'Properties from '+post_data['name'])
        {
            $(this).find('tr[typeof="rdfs:Property"]').each(function(){
                var expected_types = [];
                $(this).find('.prop-ect a').each(function(){
                    var page_url = $(this).attr('href');
                    if (page_url.match(/^http/) === null)
                    {
                        page_url = 'http://schema.org'+page_url;
                    }
                    expected_types.push({
                        'alternate_name':$(this).text(),
                        'page_url':page_url
                    });
                });
                var page_url = $(this).find('.prop-nam a').attr('href');
                if (page_url.match(/^http/) === null)
                {
                    page_url = 'http://schema.org'+page_url;
                }
                property_row.push({
                    'alternate_name':$(this).find('.prop-nam').text(),
                    'page_url':page_url,
                    'description':$(this).find('.prop-desc').text(),
                    'expected_type_row':expected_types
                });
            });
        }
    });
    if (property_row.length > 0)
    {
        post_data['property_row'] = property_row;
    }
    var children_row = [];
    $('ul').each(function(){
        if ($(this).prev().text().match(/^More specific /) !== null)
        {
            $(this).find('a').each(function(){
                var page_url = $(this).attr('href');
                if (page_url.match(/^http/) === null)
                {
                    page_url = 'http://schema.org'+page_url;
                }
                children_row.push({
                    'alternate_name':$(this).text(),
                    'page_url':page_url
                });
            });
        }
        if ($(this).prev().text().match(/but is defined in an/) !== null)
        {
            if (!post_data['name'])
            {
                post_data['page_url'] = $(this).find('a').attr('href');
                post_data['page_scanned'] = 0;
            }
        }
    });
    if (children_row.length > 0)
    {
        post_data['children_row'] = children_row;
    }
    $.ajax({
        'type': 'POST',
        'url': 'scan.php',
        'data': post_data,
        'timeout': 60000
    }).always(function (callback_obj, status, info_obj) {
        console.log(callback_obj);
        console.log(info_obj);
        if (status == 'success') {
            var data = callback_obj;
            var xhr = info_obj;
console.log(data);
            location.reload();
        }
    });
}
    $(document).ready(function(){
        console.log('document ready');
        scan_data();
    });
</script>
<?php
    endif;
endif;
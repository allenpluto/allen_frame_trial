<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 18/05/2018
 * Time: 1:57 PM
 */
define('PATH_SITE_BASE', '/wamp/www/allen_frame_trial/');
include('../../system/config/config.php');

if (!isset($_GET['root_node']))
{
    $_GET['root_node'] = 'Thing';
}

function get_node($node)
{
    if (is_numeric($node))
    {
        $entity_schema_type = new entity($node,[
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
    }
    else
    {
        $entity_schema_type = new entity(Null,[
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
        $entity_schema_type->get(['where'=>'alternate_name = "'.$node.'"']);
    }

    $row = end($entity_schema_type->row);
    $result_row = [
        'id'=>$row['id'],
        'name'=>$row['name']
    ];
    $entity_category = new entity_category();
    $entity_category->get(['where'=>'schema_itemtype = "http://schema.org/'.$row['alternate_name'].'"']);
    if (count($entity_category->row) > 0)
    {
        $result_row['old_id'] = end($entity_category->row)['id'];
    }
    else
    {
        $result_row['old_id'] = 'NOT FOUND';
    }

    if (!empty($row['schema_type']))
    {
        $result_row['children'] = [];
        $children_id_group = explode(',',$row['schema_type']);
        foreach($children_id_group as $child_id)
        {
            $result_row['children'][] = get_node($child_id);
        }
    }

    return $result_row;
}

print_r(get_node($_GET['root_node']));

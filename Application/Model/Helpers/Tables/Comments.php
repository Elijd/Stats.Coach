<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/31/17
 * Time: 6:55 PM
 */

namespace Tables;


use Modules\Helpers\Entities;
use Modules\Error\PublicAlert;
use Modules\Interfaces\iEntity;

class Comments extends Entities implements iEntity
{
    
    static function get($object, $id)
    {
        $sql = 'SELECT * FROM StatsCoach.entity_comments JOIN StatsCoach.entity_tag ON comment_id = entity_id WHERE parent_id = ? LIMIT 10';
        $object->comments = static::fetch_classes( $sql, $id );
        return true;
    }


    static function all($object, $id)
    {
        $sql = 'SELECT * FROM StatsCoach.entity_comments JOIN StatsCoach.entity_tag ON comment_id = entity_id WHERE parent_id = ?';
        $object->comments = static::fetch_classes( $sql, $id );
        return true;
    }

    static function range($object, $id, $argv)
    {
        // TODO: Implement range() method.
    }


    static function add($object, $id, $argv)
    {
        $comment_id = static::beginTransaction( 'ENTITY_COMMENTS', $id );
        $sql = 'INSERT INTO StatsCoach.entity_comments (parent_id, comment_id, user_id, comment) VALUES (:parent_id, :comment_id, :user_id, :comment)';
        $stmt = self::database()->prepare( $sql );
        $stmt->bindValue( ':parent_id', $id );
        $stmt->bindValue( ':comment_id', $entity );
        $stmt->bindValue( ':user_id', $_SESSION['id'] );
        $stmt->bindValue( ':comment', $argv );
        if ($stmt->execute()) throw new PublicAlert('Sorry, we could not process your request.', 'danger');
        return static::commit();
    }

    static function remove($object, $id)
    {
        static::remove_entity( $id );
        if (array_key_exists( $id, $object->comments ))
            unset($object->comments[$id]);
    }
}
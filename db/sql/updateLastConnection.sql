UPDATE `worktalk`.`member_of` 
SET 
    `last_connection` = current_timestamp()
WHERE
    (`user_id` = '{{user_id}}')
        AND (`chat_id` = '{{chat_id}}');

UPDATE `worktalk`.`member_of` 
SET 
    `role` = 'admin'
WHERE
    (`user_id` = '{{user_id}}')
        AND (`chat_id` = '{{chat_id}}');

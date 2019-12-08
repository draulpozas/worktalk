UPDATE `worktalk`.`member_of` 
SET 
    `role` = 'user'
WHERE
    (`user_id` = '{{user_id}}')
        AND (`chat_id` = '{{chat_id}}');

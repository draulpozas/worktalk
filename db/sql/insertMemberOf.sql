INSERT INTO `worktalk`.`member_of` (
	`user_id`, 
    `chat_id`, 
    `last_connection`
) VALUES (
	'{{user_id}}', 
    '{{chat_id}}', 
    current_timestamp()
);

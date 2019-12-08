DELETE FROM `worktalk`.`friendship` 
WHERE 
    `user_id_1` = '{{user_id_1}}' 
    AND `user_id_2` = '{{user_id_2}}';
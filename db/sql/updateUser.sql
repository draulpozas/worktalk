UPDATE `worktalk`.`user` 
SET 
    `username` = '{{username}}', 
    `passwd` = '{{passwd}}', 
    `email` = '{{email}}', 
    `lang` = '{{lang}}'
WHERE
    (`id` = '{{id}}');


WITH `classes` (`id`, `name`) AS (
  VALUES (0, 'frosh'),
    (1, 'smore'),
    (2, 'junior')
)
SELECT `assignments`.`team`,
  `users`.`name` || ' (' || `classes`.`name` || ')'
FROM `assignments`
INNER JOIN `users` ON `users`.`id` = `assignments`.`user`
INNER JOIN `classes` ON `classes`.`id` = `users`.`class`
ORDER BY `users`.`class`

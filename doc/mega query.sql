SELECT
	task_id,
	task_name,
	task_description,
	task_estimated_dev_time,
	task_update_time,
	task_create_time,
	task_status,
	task_developer,
	(SELECT user_name FROM ot_user WHERE user_id = task_developer) AS 'developer_name',
	(SELECT user_name FROM ot_user WHERE user_id = task_reporter) AS 'reporter_name',
	user_name,
	user_type,
	project_name,
	task_status_name,
	task_type_name,
	GROUP_CONCAT(
		(SELECT task_name FROM ot_task WHERE task_id IN
			(SELECT task_dependancy_task FROM ot_task_dependancy WHERE task_dependancy_dependancy = 101)
		)
	) AS 'dependancy_names',
	GROUP_CONCAT(
		task_note_time
	) AS 'task_comment_times',
	GROUP_CONCAT(
		REPLACE(task_note_comment, ',', '<COMMA>')
	) AS 'task_comments',
	GROUP_CONCAT(
		REPLACE(
			(SELECT user_name FROM ot_user WHERE user_id = task_note_creator),
			',',
			'<COMMA>'
		)
	) AS 'task_comment_creators'
FROM
	ot_task,
	ot_user,
	ot_project,
	ot_task_type,
	ot_task_status,
	ot_task_note
WHERE task_id = 101
AND user_id = 30
AND task_project = project_id
AND task_status = task_status_id
AND task_type = task_type_id
AND task_note_task_id = task_id
GROUP BY task_id
### Which department pays the highest salary currently and who is the department manager?

**There are two options to this question**:

> 1\. option: 1m 3s 821ms
> ```
> SELECT SQL_NO_CACHE
>     s.salary AS highest_salary,
>      emp.emp_no,
>      CONCAT(emp.first_name, ' ', emp.last_name) AS employee,
>      dept.dept_no,
>      dept.dept_name AS department,
>      man.emp_no AS man_no,
>      CONCAT(man.first_name, ' ', man.last_name) AS manager
> FROM salaries AS s
> INNER JOIN dept_emp AS de ON (de.emp_no = s.emp_no)
> INNER JOIN employees AS emp ON (emp.emp_no = de.emp_no)
> INNER JOIN departments AS dept ON (dept.dept_no = de.dept_no)
> INNER JOIN dept_manager AS dm ON (dm.dept_no = dept.dept_no)
> INNER JOIN employees AS man ON (man.emp_no = dm.emp_no)
> WHERE
>     NOW() BETWEEN s.from_date AND s.to_date
>     AND NOW() BETWEEN dm.from_date AND dm.to_date
> ORDER BY s.salary DESC LIMIT 1;
>```
> Result: 158220,43624,Tokuyasu Pesch,d007,Sales,111133,Hauke Zhang

> 2\. option: 27s 561ms
>
> The subquery runs the hard query in one table, then we join the other tables to get other information
> Since the subquery returns only one row this query is faster then the query above
> ```
> SELECT SQL_NO_CACHE
>     s.salary AS highest_salary,
>     emp.emp_no,
>     CONCAT(emp.first_name, ' ', emp.last_name) AS employee,
>     dept.dept_no,
>     dept.dept_name AS department,
>     man.emp_no AS man_no,
>     CONCAT(man.first_name, ' ', man.last_name) AS manager
> FROM (
>     SELECT emp_no, salary
>     FROM salaries
>     WHERE
>         NOW() BETWEEN from_date AND to_date
>     ORDER BY salary DESC
>     LIMIT 1
> ) AS s
> INNER JOIN dept_emp AS de ON (de.emp_no = s.emp_no)
> INNER JOIN employees AS emp ON (emp.emp_no = de.emp_no)
> INNER JOIN departments AS dept ON (dept.dept_no = de.dept_no)
> INNER JOIN dept_manager AS dm ON (dm.dept_no = dept.dept_no)
> INNER JOIN employees AS man ON (man.emp_no = dm.emp_no)
> WHERE
>     NOW() BETWEEN dm.from_date AND dm.to_date;
> ```
> Result: 158220,43624,Tokuyasu Pesch,d007,Sales,111133,Hauke Zhang

### How many employees are working in Human Resources Department currently?

> 1s 224ms
> ```
> SELECT SQL_NO_CACHE
>     COUNT(*) AS total_employees
> FROM dept_emp
> WHERE
>     dept_no = 'd003'
> AND NOW() BETWEEN from_date AND to_date;
> ```
> Result: 12898

### Who is currently the oldest employee of the company?

> 7s 862ms
>
> In this query we have 9 employees who started on the same date: 1985-01-01
> ```
> SELECT SQL_NO_CACHE
>     emp.emp_no,
>     CONCAT(emp.first_name, ' ', emp.last_name) AS employee,
>     emp.hire_date,
>     de.from_date
> FROM
>     employees AS emp
> INNER JOIN
>     dept_emp AS de ON (de.emp_no = emp.emp_no)
> WHERE NOW() BETWEEN de.from_date AND de.to_date
> ORDER BY
>     emp.hire_date ASC
> LIMIT 9;
> ```
> You can retrive the last employee with the option bellow
>
> ORDER BY emp.hire_date ASC, e.emp_no ASC
> LIMIT 1

### What is the current average salary of Marketing Department?

> 1s 39ms
> ```
> SELECT SQL_NO_CACHE
>     FORMAT(SUM(salary), 2) AS salary_total,
>     FORMAT(AVG(salary), 2) AS average_salary
> FROM
>     dept_emp AS de
> INNER JOIN
>     salaries AS s ON (s.emp_no = de.emp_no)
> WHERE
>     dept_no = 'd001'
> AND NOW() BETWEEN s.from_date AND s.to_date;
> ```
> Result: "1,300,398,678.00","80,014.69"

### Who is currently the newest employee of the company?

> 6s 283ms
> ```
> SELECT SQL_NO_CACHE
>     emp.emp_no,
>     CONCAT(emp.first_name, ' ', emp.last_name) AS employee,
>     emp.hire_date,
>     de.from_date
> FROM
>     employees AS emp
> INNER JOIN
>     dept_emp AS de ON (de.emp_no = emp.emp_no)
> WHERE NOW() BETWEEN de.from_date AND de.to_date
> ORDER BY
>     emp.hire_date DESC
> LIMIT 1;
> ```
> Result: 428377,Yucai Gerlach,2000-01-23,2000-01-23

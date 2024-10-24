1st first install xampp inside your pc 
2nd start xampp and start apache and mysql
3rd put the unzipped rule engine folder inside htdocs/projects/
4th set up the db thats inside rule engine/database/db.php .. create a db
5th copy paste the schema into your db to add all the sql. Scheme is inside rule engine/database/schema.sql
6th open the project inside your chrome or any browser you like 
7th http://localhost/Projects/Rule%20Engine/ this is how your url will look like 


SOME RULES TO FOLLOW AS WE WERE UNABLE TO ADD THE VALIDATIONS

### Updated Rules for Valid Conditions

1. **Must Start and End with Parentheses**:
   - Every rule must start with `(` and end with `)`.

   **Example**:
   - Valid: `(age >= 20)`
   - Invalid: `age >= 20` (missing parentheses)

2. **Single Condition**:
   - If there's only one condition, it should be wrapped in parentheses.

   **Example**:
   - Valid: `(salary < 3000)`
   - Invalid: `salary < 3000` (missing parentheses)

3. **Two Conditions with AND**:
   - If you have two conditions connected by **AND**, they should be written without extra parentheses around them.

   **Example**:
   - Valid: `(age >= 30 AND salary >= 40000)`
   - Invalid: `((age >= 30) AND (salary >= 40000))` (extra parentheses)

4. **Two Conditions with OR**:
   - If you have two conditions connected by **OR**, both conditions must be wrapped in parentheses, and there must be an outer pair of parentheses as well.

   **Example**:
   - Valid: `((department = 'IT') OR (location = 'New York'))`
   - Invalid: `(department = 'IT' OR location = 'New York')` (missing outer parentheses)

5. **No More Than Two Conditions**:
   - You cannot have more than two conditions in a rule. Rules should only consist of one or two conditions.

   **Example**:
   - Valid: `(age >= 25)` 
   - Valid: `((salary >= 40000) OR (department = 'Sales'))`
   - Invalid: `(age > 20 AND salary < 3000 AND department = 'HR')` (more than two conditions)

### Summary
- Always start and end with parentheses.
- For a single condition, use parentheses.
- For two conditions joined by **AND**, do **not


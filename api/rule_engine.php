<?php
// Include DB connection
include('../database/db.php');

// Node class for AST
class Node {
    public $type;
    public $left;
    public $right;
    public $value;

    public function __construct($type, $left = null, $right = null, $value = null) {
        $this->type = $type;
        $this->left = $left;
        $this->right = $right;
        $this->value = $value;
    }
}

// Function to create a rule from a rule string
function create_rule($rule_string) {
    $tokens = tokenize($rule_string);
    return parse_expression($tokens);
}

function tokenize($rule_string) {
    // Add spaces around operators and parentheses
    $rule_string = str_replace(['(', ')'], [' ( ', ' ) '], $rule_string);
    return explode(' ', preg_replace('/\s+/', ' ', trim($rule_string)));
}

function parse_expression(&$tokens) {
    if (empty($tokens)) {
        return null;
    }

    $token = array_shift($tokens);

    if ($token == '(') {
        $left = parse_expression($tokens);
        $operator = array_shift($tokens);
        $right = parse_expression($tokens);
        array_shift($tokens); // Closing parenthesis ')'
        return new Node("operator", $left, $right, $operator);
    } elseif (in_array($token, ['AND', 'OR'])) {
        return new Node("operator", null, null, $token);
    } else {
        return parse_condition($token, $tokens);
    }
}

function parse_condition($left_operand, &$tokens) {
    $operator = array_shift($tokens);
    $right_operand = array_shift($tokens);
    return new Node("operand", $left_operand, $right_operand, $operator);
}

// Function to combine multiple rules using OR
function combine_rules($rules) {
    $combined = null;
    foreach ($rules as $rule_string) {
        $rule_ast = create_rule($rule_string);
        if ($combined === null) {
            $combined = $rule_ast;
        } else {
            $combined = new Node("operator", $combined, $rule_ast, "OR"); // Using OR to combine rules
        }
    }
    return $combined;
}

function evaluate_rule($ast, $data) {
    // Base case: if the AST is null, return true (no conditions to check)
    if ($ast === null) {
        return true;
    }

    // If the node is an operator
    if ($ast->type === "operator") {
        $left_result = evaluate_rule($ast->left, $data);
        $right_result = evaluate_rule($ast->right, $data);

        // Check the type of operator and return the appropriate boolean value
        if ($ast->value === "AND") {
            return $left_result && $right_result; // All conditions must be true
        } elseif ($ast->value === "OR") {
            return $left_result || $right_result; // At least one condition must be true
        }
    } elseif ($ast->type === "operand") {
        // Evaluating the condition represented by this operand node
        $left_operand = $data[$ast->left]; // Fetching value from data based on left operand
        $right_operand = trim($ast->right, "'"); // Cleaning up the right operand
        $operator = $ast->value; // The operator for this condition

        // Handle numeric and string comparisons
        if (is_numeric($left_operand) && is_numeric($right_operand)) {
            // Convert right operand to number if both are numeric
            $right_operand = (float) $right_operand;
        } else {
            // Otherwise, assume it's a string and treat it as such
            $right_operand = trim($right_operand, "'"); // remove quotes for comparison
        }

        // Evaluate the condition based on the operator
        switch ($operator) {
            case '>':
                return $left_operand > $right_operand;
            case '<':
                return $left_operand < $right_operand;
            case '>=':
                return $left_operand >= $right_operand;
            case '<=':
                return $left_operand <= $right_operand;
            case '==':
                return $left_operand == $right_operand;
            case '!=':
                return $left_operand != $right_operand;
            case '=':
                return $left_operand == $right_operand;
            default:
                return false; // If the operator is unknown, return false
        }
    }

    return false; // If none of the conditions are met, return false
}

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'create_rule') {
        $rule_string = $_POST['rule_string'];

        // Validate the rule format before processing

            // Call create_rule function to process the rule string
            $ast = create_rule($rule_string);

            // Store the rule in the database
            $stmt = $pdo->prepare("INSERT INTO rules (rule_string) VALUES (?)");
            $stmt->execute([$rule_string]);

            echo "<p>Rule created successfully!</p>";

    } elseif ($_POST['action'] == 'evaluate_rule') {
        $data_json = $_POST['data'];

        // Validate if the input is a valid JSON string
        if (!isValidJson($data_json)) {
            echo "<p>Invalid JSON data. Please provide valid JSON format for evaluation.</p>";
        } else {
            $data = json_decode($data_json, true);

            // Retrieve rules from the database for evaluation
            $stmt = $pdo->query("SELECT rule_string FROM rules");
            $rules = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Combine rules
            $combined_ast = combine_rules($rules);

            // Evaluate the combined AST against the user data
            $result = evaluate_rule($combined_ast, $data);
            echo "<h3>Evaluation Result: " . ($result ? "Passed" : "Failed") . "</h3>";
        }
    }
}

function validateRule($rule) {
    // Regular expression pattern for a single valid condition wrapped in parentheses
    $validConditionPattern = '/^\(\s*([a-zA-Z_][a-zA-Z0-9_]*\s*[>=<]=?\s*(\'[^\']*\'|"[^"]*"|\d+))\s*\)$/'; // Generalized valid condition

    // Validating the input rule against the pattern
    if (preg_match($validConditionPattern, $rule)) {
        return true; // Valid rule
    }
    
    return false; // If no pattern matches, return false
}








// Server-side validation function for JSON data
function isValidJson($jsonString) {
    json_decode($jsonString);
    
    // Return false if the input is not valid JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        return false;
    }
    
    return true;
}

?>

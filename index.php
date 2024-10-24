<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rule Engine</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <?php include('templates/header.php'); ?>
    
    <h1>Rule Engine</h1>
    
    <!-- Rule creation form -->
    <form method="post" id="ruleForm" action="api/rule_engine.php">
        <label for="rule">Enter Rule:</label>
        <input type="text" id="rule" name="rule_string" required>
        <input type="hidden" name="action" value="create_rule">
        <button type="submit">Create Rule</button>
    </form>
    
    <!-- Rule evaluation form -->
    <form method="post" action="api/rule_engine.php">
        <label for="data">Enter Data (JSON):</label>
        <textarea id="data" name="data" required></textarea>
        <input type="hidden" name="action" value="evaluate_rule">
        <button type="submit">Evaluate Rule</button>
    </form>

    <?php include('templates/footer.php'); ?>
    <script src="/js/app.js"></script>
</body>
</html>

document.getElementById('createRuleForm').onsubmit = function(e) {
    e.preventDefault();
    const rule = document.getElementById('rule').value;

    fetch('/api/rule_engine.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'create_rule',
            rule_string: rule
        })
    }).then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              alert('Rule created successfully');
          }
      });
};

document.getElementById('evaluateRuleForm').onsubmit = function(e) {
    e.preventDefault();
    const data = document.getElementById('data').value;

    fetch('/api/rule_engine.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'evaluate_rule',
            data: data
        })
    }).then(response => response.json())
      .then(data => {
          alert('Eligibility: ' + data.eligible);
      });
};

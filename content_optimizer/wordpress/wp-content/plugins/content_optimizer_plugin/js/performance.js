document.getElementById("check-performance").addEventListener("click", () => {
    const url       = document.getElementById("performance_url").value.trim();
    const resultBox = document.getElementById("performance-results");
  
    if (!url) {
      resultBox.innerHTML = "<div class='optimizer-warning'>⚠️ Please enter a valid URL.</div>";
      resultBox.style.display = "block";
      return;
    }
  
    resultBox.innerHTML = "⏳ Checking performance…";
    resultBox.style.display = "block";
  
    fetch(performanceAudit.ajax_url, {
      method : "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body   : new URLSearchParams({ action: "run_performance_audit", url })
    })
    .then(res => res.json())
    .then(({ success, data }) => {
      if (!success || !data) {
        throw new Error("Invalid response");
      }
  
      resultBox.className = "optimizer-message optimizer-success";
      resultBox.innerHTML = `✅ Performance Score: <strong>${data.performance}</strong>`;
    })
    .catch(err => {
      resultBox.className = "optimizer-message optimizer-error";
      resultBox.innerHTML = `❌ Error: ${err.message}`;
      console.error("AJAX Error:", err);
    });
  });
  
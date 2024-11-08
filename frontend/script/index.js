(async function () {
  const req = await fetch(
    "http://169.239.251.102:3341/~madiba.quansah/backend/src/index.php/health",
  );
  const json = await req.json();
  const data = json.status;
  console.log(`${data}`);
})();

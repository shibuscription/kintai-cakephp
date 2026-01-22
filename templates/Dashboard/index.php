<style>
  :root{
    --bg:#0b1220;
    --card:#111a2e;
    --text:#eaf0ff;
    --muted:rgba(234,240,255,.78);
    --border:rgba(255,255,255,.12);
  }

  body{
    margin:0;
    min-height:100vh;
    background:
      radial-gradient(1200px 700px at 20% 0%, rgba(45,212,191,.18), transparent 60%),
      radial-gradient(1200px 700px at 80% 0%, rgba(249,115,22,.16), transparent 60%),
      var(--bg);
    color:var(--text);
  }

  .wrap{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
  }

  .panel{
    width:min(1100px, 100%);
    border:1px solid var(--border);
    background: rgba(17,26,46,.55);
    border-radius: 22px;
    padding: 26px 24px 24px;
    backdrop-filter: blur(6px);
    box-shadow: 0 20px 50px rgba(0,0,0,.35);
    text-align:center;
  }

  h1{
    margin:0 0 14px;
    font-size: 30px;
    letter-spacing:.5px;
  }

  #clock{
    font-size: clamp(72px, 10vw, 132px);
    font-weight: 900;
    letter-spacing: 2px;
    margin: 10px 0 8px;
    font-variant-numeric: tabular-nums;
    user-select:none;
  }

  .sub{
    margin:0 0 10px;
    color:var(--muted);
    font-size: 14px;
  }

  #status{
    margin: 10px auto 0;
    font-size: 18px;
    color: var(--muted);
    padding: 10px 14px;
    border:1px solid var(--border);
    border-radius: 999px;
    display:inline-block;
    background: rgba(0,0,0,.18);
    min-width: 320px;
  }

  /* ちょい装飾：待機アニメ */
  .dots::after{
    content:"";
    display:inline-block;
    width: 1.2em;
    text-align:left;
    animation:dots 1.2s steps(4,end) infinite;
  }
  @keyframes dots{
    0%{content:"";}
    25%{content:".";}
    50%{content:"..";}
    75%{content:"...";}
    100%{content:"";}
  }

  @media (max-width: 720px){
    h1{ font-size:24px; }
    #status{ min-width: 0; width: 100%; }
  }
</style>

<div class="wrap">
  <div class="panel">
    <h1>打刻待ち</h1>

    <div id="clock">--:--:--</div>
    <p class="sub">カードをタッチしてください</p>

    <p id="status" class="dots">カードの読み取りを待っています</p>
  </div>
</div>

<script>
/** ====== Clock (real-time, seconds) ====== **/
const clockEl = document.getElementById('clock');

function pad2(n){ return String(n).padStart(2,'0'); }
function renderClock(){
  const d = new Date();
  const hh = pad2(d.getHours());
  const mm = pad2(d.getMinutes());
  const ss = pad2(d.getSeconds());
  clockEl.textContent = `${hh}:${mm}:${ss}`;
}

// 秒境界に揃える（見た目が気持ちいい）
function startClock(){
  renderClock();
  const now = new Date();
  const msToNextSecond = 1000 - now.getMilliseconds();
  setTimeout(() => {
    renderClock();
    setInterval(renderClock, 1000);
  }, msToNextSecond);
}
startClock();

/** ====== Polling ====== **/
const statusEl = document.getElementById('status');
function setStatus(msg){
  statusEl.textContent = msg;
}

// 必須なら device_id を付ける
const DEVICE_ID = 'DEV_LOCAL';

async function poll() {
  try {
    const res = await fetch('/scan-logs/next?device_id=' + encodeURIComponent(DEVICE_ID), { cache: 'no-store' });
    const data = await res.json();

    if (data.error) {
      setStatus('エラー: ' + data.error);
      setTimeout(poll, 1000);
      return;
    }

    if (data.found) {
      setStatus('打刻を受信しました。画面を切り替えます…');
      location.href = '/attendance/decide?scan_log_id=' + data.scan_log_id;
      return;
    }

    setStatus('カードの読み取りを待っています');
  } catch (e) {
    setStatus('接続中…（再試行）');
  }
  setTimeout(poll, 1000);
}
poll();
</script>

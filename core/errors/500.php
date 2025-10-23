<!DOCTYPE html>
<html lang="tr">
	<head>
		<meta charset="utf-8" />
		<title>Sunucu Hatası</title>

		<style>
			div.logo {
			    height: 200px;
			    width: 155px;
			    display: inline-block;
			    opacity: 0.08;
			    position: absolute;
			    top: 2rem;
			    left: 50%;
			    margin-left: -73px;
			}
			body {
			    height: 100%;
			    background: #fafafa;
			    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			    color: #777;
			    font-weight: 300;
			}
			h1 {
			    font-weight: lighter;
			    letter-spacing: 0.8;
			    font-size: 3rem;
			    margin-top: 0;
			    margin-bottom: 0;
			    color: #222;
			}
			.wrap {
			    max-width: 1024px;
			    margin: 5rem auto;
			    padding: 2rem;
			    background: #fff;
			    text-align: center;
			    border: 1px solid #efefef;
			    border-radius: 0.5rem;
			    position: relative;
			}
			pre {
			    white-space: normal;
			    margin-top: 1.5rem;
			}
			code {
			    background: #fafafa;
			    border: 1px solid #efefef;
			    padding: 0.5rem 1rem;
			    border-radius: 5px;
			    display: block;
			}
			p {
			    margin-top: 1.5rem;
			}
			.footer {
			    margin-top: 2rem;
			    border-top: 1px solid #efefef;
			    padding: 1em 2em 0 2em;
			    font-size: 85%;
			    color: #999;
			}
			a:active,
			a:link,
			a:visited {
			    color: #dd4814;
			}
			.trace {
			    background: #fafafa;
			    border: 1px solid #efefef;
			    padding: 1rem;
			    border-radius: 5px;
			    margin-top: 1rem;
			    text-align: left;
			}
			.trace h2 {
			    font-size: 1.2rem;
			    margin-top: 0;
			    margin-bottom: 1rem;
			    color: #222;
			    font-weight: normal;
			}
			.trace-item {
			    margin-bottom: 1rem;
			    padding-bottom: 1rem;
			    border-bottom: 1px solid #efefef;
			}
			.trace-item:last-child {
			    border-bottom: none;
			}
			.trace-file {
			    font-family: Consolas, Monaco, 'Courier New', monospace;
			    font-size: 0.9rem;
			    color: #dd4814;
			}
			.trace-line {
			    font-family: Consolas, Monaco, 'Courier New', monospace;
			    font-size: 0.9rem;
			    color: #555;
			}
		</style>
	</head>
	<body>
		<div class="wrap">
			<h1>Whoops!</h1>
			<p>Bir hata oluştu ve işleminiz tamamlanamadı.</p>

			<code>
				<?= htmlspecialchars($severity ?? 'Error') ?>:
				<?= htmlspecialchars($message ?? 'Bir hata oluştu') ?>
			</code>

			<?php if (isset($filepath) && isset($line)): ?>
			<div class="trace">
				<h2>Hata Konumu</h2>
				<div class="trace-item">
					<div class="trace-file">
						<?= htmlspecialchars($filepath) ?>
					</div>
					<div class="trace-line">
						Satır:
						<?= htmlspecialchars($line) ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php if (isset($backtrace) && !empty($backtrace)): ?>
			<div class="trace">
				<h2>Stack Trace</h2>
				<?php foreach (array_slice($backtrace, 0, 5) as $index =>
				$trace): ?>
				<div class="trace-item">
					<div style="color: #999; margin-bottom: 0.3rem;">#<?= $index ?></div>
					<?php if (isset($trace['file'])): ?>
					<div class="trace-file">
						<?= htmlspecialchars($trace['file']) ?>
					</div>
					<?php if (isset($trace['line'])): ?>
					<div class="trace-line">
						Satır:
						<?= htmlspecialchars($trace['line']) ?>
					</div>
					<?php endif; ?>
					<?php endif; ?>
					<?php if (isset($trace['function'])): ?>
					<div style="color: #555; margin-top: 0.3rem;"><?= htmlspecialchars($trace['class'] ?? '') ?><?= htmlspecialchars($trace['type'] ?? '') ?><?= htmlspecialchars($trace['function']) ?>()</div>
					<?php endif; ?>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</body>
</html>

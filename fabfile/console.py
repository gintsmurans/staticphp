import sys
import time
import logging

class Colors:
    # Reset
    ColorOff="\033[0m"       # Text Reset

    # Regular Colors
    Black="\033[0;30m"        # Black
    Red="\033[0;31m"          # Red
    Green="\033[0;32m"      # Green
    Yellow="\033[0;33m"       # Yellow
    Blue="\033[0;34m"         # Blue
    Purple="\033[0;35m"       # Purple
    Cyan="\033[0;36m"         # Cyan
    White="\033[0;37m"        # White

    # Bold
    BBlack="\033[1;30m"       # Black
    BRed="\033[1;31m"         # Red
    BGreen="\033[1;32m"       # Green
    BYellow="\033[1;33m"      # Yellow
    BBlue="\033[1;34m"        # Blue
    BPurple="\033[1;35m"      # Purple
    BCyan="\033[1;36m"        # Cyan
    BWhite="\033[1;37m"       # White

    # Underline
    UBlack="\033[4;30m"       # Black
    URed="\033[4;31m"         # Red
    UGreen="\033[4;32m"       # Green
    UYellow="\033[4;33m"      # Yellow
    UBlue="\033[4;34m"        # Blue
    UPurple="\033[4;35m"      # Purple
    UCyan="\033[4;36m"        # Cyan
    UWhite="\033[4;37m"       # White

    # Background
    BgBlack="\033[40m"       # Black
    BgRed="\033[41m"         # Red
    BgGreen="\033[42m"       # Green
    BgYellow="\033[43m"      # Yellow
    BgBlue="\033[44m"        # Blue
    BgPurple="\033[45m"      # Purple
    BgCyan="\033[46m"        # Cyan
    BgWhite="\033[47m"       # White

    # High Intensty
    IBlack="\033[0;90m"       # Black
    IRed="\033[0;91m"         # Red
    IGreen="\033[0;92m"       # Green
    IYellow="\033[0;93m"      # Yellow
    IBlue="\033[0;94m"        # Blue
    IPurple="\033[0;95m"      # Purple
    ICyan="\033[0;96m"        # Cyan
    IWhite="\033[0;97m"       # White

    # Bold High Intensty
    BIBlack="\033[1;90m"      # Black
    BIRed="\033[1;91m"        # Red
    BIGreen="\033[1;92m"      # Green
    BIYellow="\033[1;93m"     # Yellow
    BIBlue="\033[1;94m"       # Blue
    BIPurple="\033[1;95m"     # Purple
    BICyan="\033[1;96m"       # Cyan
    BIWhite="\033[1;97m"      # White

    # High Intensty backgrounds
    BgIBlack="\033[0;100m"   # Black
    BgIRed="\033[0;101m"     # Red
    BgIGreen="\033[0;102m"   # Green
    BgIYellow="\033[0;103m"  # Yellow
    BgIBlue="\033[0;104m"    # Blue
    BgIPurple="\033[10;95m"  # Purple
    BgICyan="\033[0;106m"    # Cyan
    BgIWhite="\033[0;107m"   # White

    # Various variables you might want for your PS1 prompt instead
    Time12h=r"\T"
    Time12a=r"\@"
    PathShort=r"\w"
    PathFull=r"\W"
    NewLine="\n"
    Jobs=r"\j"


class ConsoleHandler(logging.Handler):
    def emit(self, record, **kwargs):

        # Colors
        prepend = ''
        append = ''
        if record.levelno == logging.INFO:
            prepend = Colors.Cyan
            append = Colors.ColorOff
        elif record.levelno == logging.WARNING:
            prepend = Colors.Purple
            append = Colors.ColorOff
        elif record.levelno == logging.ERROR:
            prepend = Colors.Red
            append = Colors.ColorOff

        # End char for printing to console
        end = record.end if hasattr(record, 'end') else '\n'

        # Printing to console
        level = ''
        date = ''
        if not hasattr(record, 'skip_addons') or record.skip_addons == False:
            if not hasattr(record, 'skip_level') or record.skip_level == False:
                level = '[%s] ' % (record.levelname,)

            date = ''
            if not hasattr(record, 'skip_date') or record.skip_date == False:
                date = '[%s]: ' % (time.strftime("%d.%m.%Y %H:%M:%S"),)

        if hasattr(record, 'banner'):
            # BANNER
            bannerSep = f"\n\n{Colors.Green}************************************************************{Colors.ColorOff}\n\n"
            sys.stderr.write(bannerSep)
            sys.stderr.write("%s%s%s" % (Colors.UGreen, self.format(record), Colors.ColorOff,))
            sys.stderr.write(bannerSep)
            return

        error_msg = "%s%s%s%s%s" % (prepend, level, date, self.format(record), append)
        sys.stderr.write("%s%s" % (error_msg, end))

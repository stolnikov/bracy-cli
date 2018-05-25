# BracyCLI
[![Build Status](https://scrutinizer-ci.com/g/stolnikov/bracy-cli/badges/build.png?b=code-review)](https://scrutinizer-ci.com/g/stolnikov/bracy-cli/build-status/code-review)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/stolnikov/bracy-cli/badges/quality-score.png?b=code-review)](https://scrutinizer-ci.com/g/stolnikov/bracy-cli/?branch=code-review)

CLI wrapper over [stolnikov/bracy package](https://github.com/stolnikov/bracy).

# Requirements
*  PHP >= 7.1

# Command description

  chkfile  Check for balanced brackets in a text file.

Usage:
```
  chkfile [options] [--] <fpath>
```

Arguments:
```
  fpath                                Unix file path
```

Options:
```
  -o, --opening-char[=OPENING-CHAR]    Opening character [default: "("]
  -c, --closing-char[=CLOSING-CHAR]    Closing character [default: ")"]
  -a, --allowed-chars[=ALLOWED-CHARS]  String of allowed chars excluding brace characters [default: " \n\t\r"]
```

# Usage
**Example 1.** Default parameters provided the following file contents:
```
(() )( 
  )
```
```bash
    ./console.php chkfile ./test.txt
```
Output:
```
Task completed. Brackets are balanced.
```

**Example 2.** Custom parameters provided the following file contents:
```
{ }     {}}
```
```bash
    ./console.php chkfile ./test.txt -o "{" -c "}" -a " \r\n\t"
```
Output:
```
Task completed. Brackets are unbalanced.

```
**Example 3.**
```
((*))[]
```
```bash
    ./console.php chkfile ./test.txt -o "{" -c "}" -a " \r\n\t"
```
Output:
```
Provided string contains invalid characters.
```
**Example 4.**
```
((*))[]
```
```bash
    ./console.php chkfile ./test.txt -o "[" -c "["
```
Output:
```
Opening and closing brackets must not be equal.
```
**Example 5.** Empty file is passed to the command.
```bash
    ./console.php chkfile ./test.txt
```
Output:
```
Provided string is empty.
```

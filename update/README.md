## Warning

Don't use `env()` outside of config files except in special cases.

## 注意

元はLaravel Zeroで作っていたコマンドをLaravel11のコンソールのみの構成に移行。
`env()`は.envファイルではなく環境変数からのみ読むのでconfigファイル外で使っても問題なく使える。
このような特殊な前提でのみ可能なことなので通常は`env()`をconfigファイル以外で使うのは厳禁。

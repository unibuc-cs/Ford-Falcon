import subprocess

TEST_LIST = ['LoginTest.py', 'SignupTest.py', 'CalendarFullTest.py']
TEST_FOLDER = "Selenium-tests"

def run_test(script_name):
    result = subprocess.run(['python', script_name], capture_output=True, text=True, check=True)
    print(result.stdout)
    if result.returncode != 0:
        print(f"Test {script_name} failed.")
        exit(result.returncode)

for test in TEST_LIST:
    test_path = f"{TEST_FOLDER}/{test}"
    run_test(test_path)

print("All tests passed.")
exit(0)

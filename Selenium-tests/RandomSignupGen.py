import random
import string
from selenium import webdriver

def generate_random_username(length=8):
    characters = string.ascii_letters + string.digits
    username = ''.join(random.choice(characters) for _ in range(length))
    return username

def generate_random_password(length=8):
    characters = string.ascii_letters + string.digits
    password = ''.join(random.choice(characters) for _ in range(length))
    return password

def generate_random_email(username):
    domains = ["gMGail.com", "yayahoo.com", "hothothotmail.com", "outlooklooklook.com"]
    email = f"{username}@{random.choice(domains)}"
    return email
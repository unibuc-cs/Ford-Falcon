from datetime import date
import mysql.connector #type: ignore

class dbCleaner:
    def __init__(self, user, password, test_event):
        self.USER = user
        self.PASSWORD = password
        self.TEST_EVENT = test_event
        self.TODAY = date.today()
        self.DAY = self.TODAY.day
        self.MONTH = self.TODAY.month - 1
        self.YEAR = self.TODAY.year

    def clean(self):
        db_connection = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="development"
        )

        cursor = db_connection.cursor()
        query = f"SELECT id FROM calendar WHERE name = '{self.TEST_EVENT}';"
        cursor.execute(query)
        result = cursor.fetchall()

        if result:
            calendar_id = result[0]

            query = f"DELETE FROM event WHERE calendarId IN (SELECT id FROM calendar WHERE id = {calendar_id});"
            cursor.execute(query)
            cursor.fetchall()

            query = f"DELETE FROM userincalendar WHERE calendarId IN (SELECT id FROM calendar WHERE id = {calendar_id});"
            cursor.execute(query)
            cursor.fetchall()

            query = f"DELETE FROM comments WHERE calendar_id IN (SELECT id FROM calendar WHERE id = {calendar_id});"
            cursor.execute(query)
            cursor.fetchall()

            query = f"DELETE FROM calendar WHERE id = {calendar_id};"
            cursor.execute(query)
            cursor.fetchall()

        cursor.close()
        db_connection.close()

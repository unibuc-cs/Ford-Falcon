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
        results = cursor.fetchall()

        if results:
            calendar_id = results[0][0]

            # Delete events for the specific calendar
            query = f"DELETE FROM event WHERE calendarId = {calendar_id}"
            cursor.execute(query)
            db_connection.commit()

            # Delete comments for the specific calendar
            query = f"DELETE FROM comments WHERE calendar_id = {calendar_id}"
            cursor.execute(query)
            db_connection.commit()

            query = f"DELETE FROM userincalendar WHERE calendarId = {calendar_id}"
            cursor.execute(query)
            db_connection.commit()

            # Delete the calendar
            query = f"DELETE FROM calendar WHERE id = {calendar_id}"
            cursor.execute(query)
            db_connection.commit()

        cursor.close()
        db_connection.close()

def main():
    dbc = dbCleaner('test_user', '1234', 'Test_Event_Selenium_1')
    dbc.clean()

main()
#!/usr/bin/env
#####################################################################
#              Hiho clean the table who associate with videos table.
#              Author: Minco Wang <haiming.wang@autotiming.com>
#####################################################################
import MySQLdb
import time
import logging
import ConfigParser
import socket


class Cleaner:
    def __init__(self):
        log_file = time.strftime("%Y-%m-%d") + ".log"
        logging.basicConfig(level=logging.WARNING,
                            format='%(asctime)s %(filename)s[line:%(lineno)d] %(levelname)s %(message)s',
                            datefmt='%a, %d %b %Y %H:%M:%S',
                            filename='log/' + log_file,
                            filemode='a')
        config = ConfigParser.ConfigParser()
        config.read("config/config.ini")
        self.db_host = config.get("Preference", "db_host")
        self.db_port = int(config.get("Preference", "db_port"))
        self.db_name = config.get("Preference", "db_name")
        self.db_user = config.get("Preference", "db_user")
        self.db_password = config.get("Preference", "db_password")
        socket.setdefaulttimeout(9999)

    def get_connection(self):
        return MySQLdb.connect(host=self.db_host, port=self.db_port, user=self.db_user, passwd=self.db_password,
                               db=self.db_name, )

    def clean_video(self):
        soft_delete_count = {}
        hard_delete_count = {}
        tables = ["comments", "favorites", "fragments", "fragments_resources", "subtitles",
                  "videos_content_rating", "videos_info", "videos_pictures",
                  "videos_resources", "videos_tags", "annotations", "highlights",
                  "playlists_fragments", "questions", "subtitles_fulltext", "teachers_videos",
                  "topics_videos", "videos_attachments", "videos_categories", "videos_content_rating",
                  "videos_references", "videos_specialities"
        ]
        not_have_deleted_at = set(
            ['playlists_fragments', 'videos_specialities', 'subtitles_fulltext', 'teachers_videos', 'topics_videos',
             'videos_categories'])
        db = None
        cursor = None
        try:
            db = self.get_connection()
            cursor = db.cursor()
        except MySQLdb.Error, e:
            logging.error(e)
            raise e
        sql_update_tpl = "UPDATE %s c SET c.deleted_at = NOW() WHERE EXISTS (SELECT 1 FROM videos v WHERE v.video_id = c.video_id AND v.deleted_at IS NOT NULL AND c.deleted_at IS NULL) ;"
        sql_delete_tpl = "DELETE from %s WHERE video_id not in (SELECT v.video_id FROM videos v) ;"
        try:
            for table in tables:
                if table not in not_have_deleted_at:
                    soft_delete_count[table] = cursor.execute(sql_update_tpl % table)
                hard_delete_count[table] = cursor.execute(sql_delete_tpl % table)
            db.commit()
        except MySQLdb.Error, e:
            db.rollback()
            logging.error(e.message+'['+table+']')
        db.close()
        print 'soft_delete:' + soft_delete_count.__str__()
        print 'hard_delete:' + hard_delete_count.__str__()


    def clean_fragment(self):
            soft_delete_count = {}
            hard_delete_count = {}
            tables = ["comments",
                      "fragments_shares",
                      "fragments_tags",
                      "playlists_fragments"
            ]
            not_have_deleted_at = set(['playlists_fragments'])
            db = None
            cursor = None
            try:
                db = self.get_connection()
                cursor = db.cursor()
            except MySQLdb.Error, e:
                logging.error(e)
                raise e
            sql_update_tpl = "UPDATE %s c SET c.deleted_at = NOW() WHERE EXISTS (SELECT 1 FROM fragments v WHERE v.id = c.fragment_id AND v.deleted_at IS NOT NULL AND c.deleted_at IS NULL) ;"
            sql_delete_tpl = "DELETE from %s WHERE fragment_id not in (SELECT v.id FROM fragments v) ;"
            try:
                for table in tables:
                    if table not in not_have_deleted_at:
                        soft_delete_count[table] = cursor.execute(sql_update_tpl % table)
                    hard_delete_count[table] = cursor.execute(sql_delete_tpl % table)
                db.commit()
            except MySQLdb.Error, e:
                db.rollback()
                logging.error(e.message+'['+table+']')
            db.close()
            print 'soft_delete:' + soft_delete_count.__str__()
            print 'hard_delete:' + hard_delete_count.__str__()

    def clean_teacher_department(self):
        hard_delete_count = {}
        db = None
        cursor = None
        try:
            db = self.get_connection()
            cursor = db.cursor()
        except MySQLdb.Error, e:
            logging.error(e)
            raise e
        sql_delete_tpl = "DELETE from departments_teachers WHERE teacher_id not in (SELECT t.id FROM teachers t) or department_id not in (SELECT d.id FROM departments d);"
        try:
            hard_delete_count['departments_teachers'] = cursor.execute(sql_delete_tpl)
            db.commit()
        except MySQLdb.Error, e:
            db.rollback()
            logging.error(e)
        db.close()
        print 'hard_delete:' + hard_delete_count.__str__()

    def clean_recommends(self):
        table = 'recommends'
        soft_delete_count = {}
        db = None
        cursor = None
        try:
            db = self.get_connection()
            cursor = db.cursor()
        except MySQLdb.Error, e:
            logging.error(e)
            raise e
        sql_update_tpl_video = "UPDATE %s c SET c.deleted_at = NOW() WHERE EXISTS (SELECT 1 FROM videos v WHERE v.video_id = c.content_id AND v.deleted_at IS NOT NULL AND c.deleted_at IS NULL and c.type='video') ;"
        sql_update_tpl_teacher = "UPDATE %s c SET c.deleted_at = NOW() WHERE EXISTS (SELECT 1 FROM teachers t WHERE t.id = c.content_id AND t.deleted_at IS NOT NULL AND c.deleted_at IS NULL and c.type='teacher') ;"
        try:
            soft_delete_count[table] = cursor.execute(sql_update_tpl_video % table)
            soft_delete_count[table] += cursor.execute(sql_update_tpl_teacher % table)
            db.commit()
        except MySQLdb.Error, e:
            db.rollback()
            logging.error(e)
        db.close()
        print 'soft_delete:' + soft_delete_count.__str__()

    def clean_tags(self):
            hard_delete_count = {}
            db = None
            cursor = None
            try:
                db = self.get_connection()
                cursor = db.cursor()
            except MySQLdb.Error, e:
                logging.error(e)
                raise e
            sql_delete_tpl = "DELETE from %s WHERE tag_id not in (SELECT t.id FROM tags t);"
            try:
                hard_delete_count['fragments_tags'] = cursor.execute(sql_delete_tpl % 'fragments_tags')
                hard_delete_count['videos_tags'] = cursor.execute(sql_delete_tpl % 'videos_tags')
                db.commit()
            except MySQLdb.Error, e:
                db.rollback()
                logging.error(e)
            db.close()
            print 'hard_delete:' + hard_delete_count.__str__()

    def clean_topic(self):
        hard_delete_count = {}
        db = None
        cursor = None
        table = 'topics_videos'
        try:
            db = self.get_connection()
            cursor = db.cursor()
        except MySQLdb.Error, e:
            logging.error(e)
            raise e
        sql_delete_tpl = "DELETE from %s WHERE topic_id not in (SELECT v.id FROM topics v) or topic_id in (SELECT v.id FROM topics v  where v.deleted_at IS NOT NULL );"
        try:
            hard_delete_count[table] = cursor.execute(sql_delete_tpl % table)
            db.commit()
        except MySQLdb.Error, e:
            db.rollback()
            logging.error(e.message+'['+table+']')
        db.close()
        print 'hard_delete:' + hard_delete_count.__str__()


    def clean_playlist(self):
        hard_delete_count = {}
        db = None
        cursor = None
        try:
            db = self.get_connection()
            cursor = db.cursor()
        except MySQLdb.Error, e:
            logging.error(e)
            raise e
        sql_delete_tpl = "DELETE from %s WHERE playlist_id not in (SELECT v.id FROM playlists v) or playlist_id in (SELECT v.id FROM playlists v  where v.deleted_at IS NOT NULL );"
        try:
            hard_delete_count['playlists_fragments'] = cursor.execute(sql_delete_tpl % 'playlists_fragments')
            hard_delete_count['playlists_favorites'] = cursor.execute(sql_delete_tpl % 'playlists_favorites')
            db.commit()
        except MySQLdb.Error, e:
            db.rollback()
            logging.error(e)
        db.close()
        print 'hard_delete:' + hard_delete_count.__str__()

    def clean_category(self):
        hard_delete_count = {}
        db = None
        cursor = None
        table = 'videos_categories'
        try:
            db = self.get_connection()
            cursor = db.cursor()
        except MySQLdb.Error, e:
            logging.error(e)
            raise e
        sql_delete_tpl = "DELETE from %s WHERE category_id not in (SELECT v.id FROM categories v) or category_id in (SELECT v.id FROM categories v  where v.deleted_at IS NOT NULL );"
        try:
            hard_delete_count[table] = cursor.execute(sql_delete_tpl % table)
            db.commit()
        except MySQLdb.Error, e:
            db.rollback()
            logging.error(e.message+'['+table+']')
        db.close()
        print 'hard_delete:' + hard_delete_count.__str__()

    def clean_teacher(self):
        hard_delete_count = {}
        db = None
        cursor = None
        table = 'teachers_videos'
        try:
            db = self.get_connection()
            cursor = db.cursor()
        except MySQLdb.Error, e:
            logging.error(e)
            raise e
        sql_delete_tpl = "DELETE from %s WHERE teacher_id not in (SELECT v.id FROM teachers v) or teacher_id in (SELECT v.id FROM teachers v  where v.deleted_at IS NOT NULL );"
        try:
            hard_delete_count[table] = cursor.execute(sql_delete_tpl % table)
            db.commit()
        except MySQLdb.Error, e:
            db.rollback()
            logging.error(e.message+'['+table+']')
        db.close()
        print 'hard_delete:' + hard_delete_count.__str__()

if __name__ == '__main__':
    config = ConfigParser.ConfigParser()
    config.read("config/config.ini")
    interval = int(config.get("Preference", "interval"))
    vc = Cleaner()
    while True:
        vc.clean_video()
        vc.clean_fragment()
        vc.clean_teacher_department()
        vc.clean_recommends()
        vc.clean_tags()
        vc.clean_topic()
        vc.clean_playlist()
        vc.clean_category()
        vc.clean_teacher()
        time.sleep(interval)

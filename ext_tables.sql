#
# Table structure for table 'sys_file_metadata'
#
CREATE TABLE sys_file_metadata (
	copyright_notice varchar(255) DEFAULT '' NOT NULL,
	aperture_value float unsigned DEFAULT '0' NOT NULL,
	shutter_speed_value varchar(24) DEFAULT '' NOT NULL,
	iso_speed_ratings varchar(24) DEFAULT '' NOT NULL,
	camera_model varchar(255) DEFAULT '' NOT NULL,
	focal_length int(4) unsigned DEFAULT '0' NOT NULL,
	flash int(4) unsigned DEFAULT '0' NOT NULL,
	metering_mode int(4) unsigned DEFAULT '0' NOT NULL,
	horizontal_resolution int(8) DEFAULT '0' NOT NULL,
	vertical_resolution int(8) DEFAULT '0' NOT NULL,
);
import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { API } from '../src/api';

type NotificationBadgeProps = {
  userType: 'doctor' | 'clinic';
};

const NotificationBadge: React.FC<NotificationBadgeProps> = ({ userType }) => {
  const [unreadCount, setUnreadCount] = useState<number>(0);

  const fetchUnreadCount = async () => {
    try {
      const endpoint = userType === 'doctor' 
        ? '/notifications/unread-count'
        : '/clinic/notifications/unread-count';
        
      const response = await API.get(endpoint);
      if (response.data && typeof response.data.count === 'number') {
        setUnreadCount(response.data.count);
      }
    } catch (error) {
      console.error('Error fetching unread notifications count:', error);
    }
  };

  // Fetch unread count when component mounts
  useEffect(() => {
    fetchUnreadCount();
    
    // Set up a refresh interval (every 60 seconds)
    const intervalId = setInterval(() => {
      fetchUnreadCount();
    }, 60000);
    
    return () => clearInterval(intervalId);
  }, [userType]);

  if (unreadCount === 0) {
    return null;
  }

  return (
    <View style={styles.badge}>
      <Text style={styles.badgeText}>
        {unreadCount > 99 ? '99+' : unreadCount}
      </Text>
    </View>
  );
};

const styles = StyleSheet.create({
  badge: {
    position: 'absolute',
    right: -6,
    top: -3,
    backgroundColor: '#FF3B30',
    borderRadius: 12,
    height: 18,
    minWidth: 18,
    justifyContent: 'center',
    alignItems: 'center',
  },
  badgeText: {
    color: 'white',
    fontSize: 10,
    fontWeight: 'bold',
    paddingHorizontal: 4,
  },
});

export default NotificationBadge;
import React from 'react';
import { View, Text, StyleSheet, ActivityIndicator } from 'react-native';
import { AvailabilitySummary as AvailabilitySummaryType } from '../api';

interface AvailabilitySummaryProps {
  availabilityData: AvailabilitySummaryType | null;
  isLoading: boolean;
  error?: string | null;
}

const AvailabilitySummary: React.FC<AvailabilitySummaryProps> = ({ 
  availabilityData, 
  isLoading, 
  error 
}) => {
  if (isLoading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#4f46e5" />
        <Text style={styles.loadingText}>Loading availability information...</Text>
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>Error: {error}</Text>
      </View>
    );
  }

  if (!availabilityData) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>No availability data found</Text>
      </View>
    );
  }

  const { today, next_week } = availabilityData;

  return (
    <View style={styles.container}>
      <View style={styles.todayContainer}>
        <Text style={styles.sectionTitle}>Today's Availability</Text>
        {today.is_closed ? (
          <Text style={styles.closedText}>Clinic is closed today</Text>
        ) : (
          <View style={styles.statsContainer}>
            <Text style={styles.statItem}>
              Available slots: <Text style={styles.highlightText}>{today.remaining_slots}</Text>
            </Text>
            <Text style={styles.statItem}>
              Booked appointments: <Text style={styles.highlightText}>{today.booked_count}</Text>
            </Text>
            <Text style={styles.statItem}>
              Daily limit: <Text style={styles.highlightText}>{today.daily_limit}</Text>
            </Text>
          </View>
        )}
      </View>

      <View style={styles.weekContainer}>
        <Text style={styles.sectionTitle}>Next 7 Days</Text>
        {next_week.map((day, index) => (
          <View key={index} style={styles.dayItem}>
            <View style={styles.dayHeader}>
              <Text style={styles.dayName}>{day.day_name}</Text>
              <Text style={styles.dayDate}>{formatDate(day.date)}</Text>
            </View>
            
            {day.is_closed ? (
              <Text style={styles.closedText}>Closed</Text>
            ) : (
              <View style={styles.dayStats}>
                <Text style={[
                  styles.availabilityText,
                  day.remaining_slots === 0 ? styles.fullText : 
                  day.remaining_slots < 5 ? styles.limitedText : styles.availableText
                ]}>
                  {day.remaining_slots === 0 ? 'Fully booked' : 
                   day.remaining_slots < 5 ? 'Limited availability' : 'Available'}
                </Text>
                <Text style={styles.slotsText}>
                  {day.remaining_slots}/{day.daily_limit} slots
                </Text>
              </View>
            )}
          </View>
        ))}
      </View>
    </View>
  );
};

const formatDate = (dateStr: string): string => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

const styles = StyleSheet.create({
  container: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 16,
    marginVertical: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  loadingContainer: {
    padding: 20,
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingText: {
    marginTop: 10,
    color: '#666',
  },
  errorContainer: {
    padding: 16,
    backgroundColor: '#fee2e2',
    borderRadius: 8,
  },
  errorText: {
    color: '#b91c1c',
    fontSize: 14,
  },
  todayContainer: {
    marginBottom: 16,
    paddingBottom: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#e5e7eb',
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 12,
    color: '#111827',
  },
  statsContainer: {
    flexDirection: 'column',
    gap: 8,
  },
  statItem: {
    fontSize: 15,
    color: '#4b5563',
  },
  highlightText: {
    fontWeight: '600',
    color: '#111827',
  },
  weekContainer: {
    gap: 12,
  },
  dayItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#f3f4f6',
  },
  dayHeader: {
    flex: 1,
  },
  dayName: {
    fontSize: 15,
    fontWeight: '600',
    color: '#111827',
  },
  dayDate: {
    fontSize: 13,
    color: '#6b7280',
  },
  dayStats: {
    alignItems: 'flex-end',
  },
  availabilityText: {
    fontSize: 14,
    fontWeight: '600',
  },
  availableText: {
    color: '#047857',
  },
  limitedText: {
    color: '#b45309',
  },
  fullText: {
    color: '#b91c1c',
  },
  slotsText: {
    fontSize: 13,
    color: '#6b7280',
  },
  closedText: {
    fontSize: 14,
    fontWeight: '500',
    color: '#b91c1c',
  },
});

export default AvailabilitySummary;
